<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PokemonControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::clear();
    }

    public function test_can_list_pokemon_with_mocked_pokeapi()
    {
        Http::fake([
            'https://pokeapi.co/api/v2/pokemon?limit=2000' => Http::response([
                'results' => [
                    ['name' => 'bulbasaur', 'url' => 'https://pokeapi.co/api/v2/pokemon/1/'],
                    ['name' => 'charmander', 'url' => 'https://pokeapi.co/api/v2/pokemon/4/'],
                    ['name' => 'squirtle', 'url' => 'https://pokeapi.co/api/v2/pokemon/7/'],
                ]
            ], 200),
            'https://pokeapi.co/api/v2/pokemon/bulbasaur' => Http::response([
                'id' => 1,
                'name' => 'bulbasaur',
                'sprites' => [
                    'front_default' => 'bulbasaur_sprite.png',
                    'other' => ['official-artwork' => ['front_default' => 'bulbasaur_artwork.png']]
                ],
                'types' => [['type' => ['name' => 'grass']]],
                'height' => 7,
                'weight' => 69,
                'stats' => [['stat' => ['name' => 'hp'], 'base_stat' => 45]]
            ], 200),
            'https://pokeapi.co/api/v2/pokemon/charmander' => Http::response([
                'id' => 4,
                'name' => 'charmander',
                'sprites' => [
                    'front_default' => 'charmander_sprite.png',
                ],
                'types' => [['type' => ['name' => 'fire']]],
                'height' => 6,
                'weight' => 85,
                'stats' => [['stat' => ['name' => 'hp'], 'base_stat' => 39]]
            ], 200),
            'https://pokeapi.co/api/v2/pokemon/squirtle' => Http::response([
                'id' => 7,
                'name' => 'squirtle',
                'sprites' => [
                    'front_default' => 'squirtle_sprite.png',
                ],
                'types' => [['type' => ['name' => 'water']]],
                'height' => 5,
                'weight' => 90,
                'stats' => [['stat' => ['name' => 'hp'], 'base_stat' => 44]]
            ], 200),
        ]);

        $response = $this->getJson('/api/v1/pokemon?page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'per_page',
                'total',
                'last_page'
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_search_pokemon_list()
    {
        Http::fake([
            'https://pokeapi.co/api/v2/pokemon?limit=2000' => Http::response([
                'results' => [
                    ['name' => 'bulbasaur', 'url' => 'https://pokeapi.co/api/v2/pokemon/1/'],
                    ['name' => 'charmander', 'url' => 'https://pokeapi.co/api/v2/pokemon/4/'],
                ]
            ], 200),
            'https://pokeapi.co/api/v2/pokemon/charmander' => Http::response([
                'id' => 4,
                'name' => 'charmander',
                'sprites' => [
                    'front_default' => 'charmander_sprite.png',
                ],
                'types' => [['type' => ['name' => 'fire']]],
                'height' => 6,
                'weight' => 85,
                'stats' => []
            ], 200),
        ]);

        $response = $this->getJson('/api/v1/pokemon?search=char');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'charmander');
    }

    public function test_can_show_pokemon_detail()
    {
        Http::fake([
            'https://pokeapi.co/api/v2/pokemon/bulbasaur' => Http::response([
                'id' => 1,
                'name' => 'bulbasaur',
                'sprites' => [
                    'front_default' => 'bulbasaur_sprite.png',
                ],
                'types' => [['type' => ['name' => 'grass']]],
                'height' => 7,
                'weight' => 69,
                'stats' => [['stat' => ['name' => 'hp'], 'base_stat' => 45]]
            ], 200),
        ]);

        $response = $this->getJson('/api/v1/pokemon/bulbasaur');

        $response->assertStatus(200)
            ->assertJsonPath('name', 'bulbasaur')
            ->assertJsonPath('id', 1);
    }

    public function test_returns_404_if_pokemon_not_found()
    {
        Http::fake([
            'https://pokeapi.co/api/v2/pokemon/missingno' => Http::response(null, 404),
        ]);

        $response = $this->getJson('/api/v1/pokemon/missingno');

        $response->assertStatus(404);
    }
}
