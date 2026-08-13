<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AiManagerService;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery\MockInterface;

class IaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_identify_pokemon_from_image_upload()
    {
        $user = User::factory()->create();
        
        $this->mock(AiManagerService::class, function (MockInterface $mock) {
            $mock->shouldReceive('identifyPokemon')
                ->once()
                ->andReturn([
                    'name' => 'pikachu',
                    'confidence' => 0.95,
                    'type' => 'electric',
                    'suggestion' => 'Pikachu is great for fast electric attacks!'
                ]);
        });

        $fakeImage = UploadedFile::fake()->create('pokemon.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ia/identify-pokemon', [
                'image' => $fakeImage
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'pikachu',
                'confidence' => 0.95,
                'type' => 'electric',
                'suggestion' => 'Pikachu is great for fast electric attacks!'
            ]);
    }

    public function test_get_collection_insights()
    {
        $user = User::factory()->create();

        $this->mock(AiManagerService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getCollectionInsights')
                ->once()
                ->andReturn('Your collection is well balanced with electric and fire types.');
        });

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ia/chat-insights');

        $response->assertStatus(200)
            ->assertJson([
                'insights' => 'Your collection is well balanced with electric and fire types.'
            ]);
    }
}

