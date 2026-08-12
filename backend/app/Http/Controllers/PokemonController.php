<?php

namespace App\Http\Controllers;

use App\Services\PokeApiService;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    protected PokeApiService $pokeApiService;

    public function __construct(PokeApiService $pokeApiService)
    {
        $this->pokeApiService = $pokeApiService;
    }

    /**
     * List Pokemon with optional search query and pagination.
     */
    public function index(Request $request)
    {
        $page = (int) $request->query('page', 1);
        $search = $request->query('search');

        $result = $this->pokeApiService->getPokemonList($page, $search);

        return response()->json($result);
    }

    /**
     * Show details of a single Pokemon.
     */
    public function show(string $nameOrId)
    {
        $details = $this->pokeApiService->getPokemonDetails($nameOrId);

        if (!$details) {
            return response()->json([
                'message' => 'Pokémon no encontrado en la PokéAPI.'
            ], 404);
        }

        return response()->json($details);
    }
}
