<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PokemonCollection;
use App\Services\AiManagerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery\MockInterface;

class CollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_their_collection()
    {
        $user = User::factory()->create();
        $pokemon = PokemonCollection::create([
            'user_id' => $user->id,
            'pokemon_id' => 25,
            'pokemon_name' => 'pikachu',
            'pokemon_type' => 'electric',
            'custom_notes' => 'Best buddy',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/collection');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.pokemon_name', 'pikachu');
    }

    public function test_guest_cannot_list_collection()
    {
        $response = $this->getJson('/api/v1/collection');
        $response->assertStatus(401);
    }

    public function test_user_can_add_pokemon_to_collection()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/collection', [
                'pokemon_id' => 4,
                'pokemon_name' => 'Charmander',
                'pokemon_type' => 'fire',
                'custom_notes' => 'Loves hot places',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.pokemon_name', 'charmander');

        $this->assertDatabaseHas('pokemon_collections', [
            'user_id' => $user->id,
            'pokemon_id' => 4,
            'pokemon_name' => 'charmander',
        ]);
    }

    public function test_user_cannot_add_duplicate_pokemon()
    {
        $user = User::factory()->create();
        PokemonCollection::create([
            'user_id' => $user->id,
            'pokemon_id' => 25,
            'pokemon_name' => 'pikachu',
            'pokemon_type' => 'electric',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/collection', [
                'pokemon_id' => 25,
                'pokemon_name' => 'Pikachu',
                'pokemon_type' => 'electric',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Este Pokémon ya está en tu colección.');
    }

    public function test_user_can_update_custom_notes()
    {
        $user = User::factory()->create();
        $pokemon = PokemonCollection::create([
            'user_id' => $user->id,
            'pokemon_id' => 25,
            'pokemon_name' => 'pikachu',
            'pokemon_type' => 'electric',
            'custom_notes' => 'Old notes',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/collection/{$pokemon->id}", [
                'custom_notes' => 'New updated notes',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.custom_notes', 'New updated notes');

        $this->assertDatabaseHas('pokemon_collections', [
            'id' => $pokemon->id,
            'custom_notes' => 'New updated notes',
        ]);
    }

    public function test_user_can_remove_pokemon_from_collection()
    {
        $user = User::factory()->create();
        $pokemon = PokemonCollection::create([
            'user_id' => $user->id,
            'pokemon_id' => 25,
            'pokemon_name' => 'pikachu',
            'pokemon_type' => 'electric',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/collection/{$pokemon->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('pokemon_collections', [
            'id' => $pokemon->id,
        ]);
    }

    public function test_user_cannot_add_pokemon_with_inappropriate_notes()
    {
        $user = User::factory()->create();

        $this->mock(AiManagerService::class, function (MockInterface $mock) {
            $mock->shouldReceive('checkTextModeration')
                ->once()
                ->with('some bad words')
                ->andReturn(false);
        });

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/collection', [
                'pokemon_id' => 4,
                'pokemon_name' => 'Charmander',
                'pokemon_type' => 'fire',
                'custom_notes' => 'some bad words',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'El texto ingresado contiene lenguaje inapropiado y viola las políticas de moderación.');

        $this->assertDatabaseMissing('pokemon_collections', [
            'user_id' => $user->id,
            'pokemon_id' => 4,
        ]);
    }

    public function test_user_cannot_update_pokemon_with_inappropriate_notes()
    {
        $user = User::factory()->create();
        $pokemon = PokemonCollection::create([
            'user_id' => $user->id,
            'pokemon_id' => 25,
            'pokemon_name' => 'pikachu',
            'pokemon_type' => 'electric',
            'custom_notes' => 'Old clean notes',
        ]);

        $this->mock(AiManagerService::class, function (MockInterface $mock) {
            $mock->shouldReceive('checkTextModeration')
                ->once()
                ->with('some bad words update')
                ->andReturn(false);
        });

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/collection/{$pokemon->id}", [
                'custom_notes' => 'some bad words update',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'El texto ingresado contiene lenguaje inapropiado y viola las políticas de moderación.');

        $this->assertDatabaseHas('pokemon_collections', [
            'id' => $pokemon->id,
            'custom_notes' => 'Old clean notes',
        ]);
    }
}
