<?php

namespace App\Http\Controllers;

use App\Models\PokemonCollection;
use App\Services\AiManagerService;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    protected AiManagerService $aiService;

    public function __construct(AiManagerService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get the authenticated user's Pokemon collection.
     */
    public function index(Request $request)
    {
        $collection = $request->user()->pokemonCollections()->orderBy('created_at', 'desc')->get();
        return response()->json($collection);
    }

    /**
     * Add a Pokemon to the collection.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pokemon_id' => 'required|integer',
            'pokemon_name' => 'required|string',
            'pokemon_type' => 'required|string',
            'custom_notes' => 'nullable|string',
        ]);

        // Moderation check using the fallback-enabled AiManagerService
        if ($request->filled('custom_notes')) {
            if (!$this->aiService->checkTextModeration($request->custom_notes)) {
                return response()->json([
                    'message' => 'El texto ingresado contiene lenguaje inapropiado y viola las políticas de moderación.'
                ], 422);
            }
        }

        $user = $request->user();

        // Check for duplicates
        $exists = $user->pokemonCollections()
            ->where('pokemon_id', $request->pokemon_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Este Pokémon ya está en tu colección.'
            ], 422);
        }

        $pokemon = $user->pokemonCollections()->create([
            'pokemon_id' => $request->pokemon_id,
            'pokemon_name' => strtolower($request->pokemon_name),
            'pokemon_type' => $request->pokemon_type,
            'custom_notes' => $request->custom_notes,
        ]);

        return response()->json([
            'message' => 'Pokémon guardado en la colección.',
            'data' => $pokemon
        ], 201);
    }

    /**
     * Update custom notes for a Pokemon in the collection.
     */
    public function update(Request $request, $id)
    {
        $pokemon = $request->user()->pokemonCollections()->findOrFail($id);

        $request->validate([
            'custom_notes' => 'nullable|string',
        ]);

        // Moderation check using the fallback-enabled AiManagerService
        if ($request->filled('custom_notes')) {
            if (!$this->aiService->checkTextModeration($request->custom_notes)) {
                return response()->json([
                    'message' => 'El texto ingresado contiene lenguaje inapropiado y viola las políticas de moderación.'
                ], 422);
            }
        }

        $pokemon->update([
            'custom_notes' => $request->custom_notes,
        ]);

        return response()->json([
            'message' => 'Notas actualizadas correctamente.',
            'data' => $pokemon
        ]);
    }

    /**
     * Remove a Pokemon from the collection.
     */
    public function destroy(Request $request, $id)
    {
        $pokemon = $request->user()->pokemonCollections()->findOrFail($id);
        $pokemon->delete();

        return response()->json([
            'message' => 'Pokémon eliminado de la colección.'
        ]);
    }
}
