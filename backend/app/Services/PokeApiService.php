<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PokeApiService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('POKEAPI_BASE_URL', 'https://pokeapi.co/api/v2'), '/');
    }

    /**
     * Get a paginated list of Pokemon, optionally filtered by a search query.
     */
    public function getPokemonList(int $page = 1, ?string $search = null, int $perPage = 20): array
    {
        // Cache the list of ALL Pokemon names and URLs to allow local search
        // There are around 1300+ Pokemon, which is a tiny JSON payload.
        $allPokemon = Cache::remember('pokeapi_all_pokemon', 86400, function () {
            $response = Http::get("{$this->baseUrl}/pokemon?limit=2000");
            if ($response->successful()) {
                return $response->json()['results'] ?? [];
            }
            return [];
        });

        // Filter by search query if present
        if (!empty($search)) {
            $search = strtolower(trim($search));
            $allPokemon = array_values(array_filter($allPokemon, function ($pokemon) use ($search) {
                return str_contains(strtolower($pokemon['name']), $search);
            }));
        }

        $total = count($allPokemon);
        $offset = ($page - 1) * $perPage;
        $paginatedResults = array_slice($allPokemon, $offset, $perPage);

        // Fetch details (images, types) for each pokemon in the current page
        $items = [];
        foreach ($paginatedResults as $item) {
            $details = $this->getPokemonDetails($item['name']);
            if ($details) {
                $items[] = $details;
            }
        }

        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Get details of a single Pokemon by name or ID.
     */
    public function getPokemonDetails(string $nameOrId): ?array
    {
        $cacheKey = "pokemon_detail_" . strtolower($nameOrId);

        return Cache::remember($cacheKey, 86400, function () use ($nameOrId) {
            $response = Http::get("{$this->baseUrl}/pokemon/" . strtolower($nameOrId));
            if ($response->successful()) {
                $data = $response->json();
                
                // Extract only needed properties to keep cache/response clean
                return [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'sprite' => $data['sprites']['other']['official-artwork']['front_default'] ?? $data['sprites']['front_default'] ?? null,
                    'types' => array_map(function ($t) {
                        return $t['type']['name'];
                    }, $data['types'] ?? []),
                    'height' => $data['height'],
                    'weight' => $data['weight'],
                    'stats' => array_map(function ($s) {
                        return [
                            'name' => $s['stat']['name'],
                            'value' => $s['base_stat'],
                        ];
                    }, $data['stats'] ?? []),
                ];
            }
            return null;
        });
    }
}
