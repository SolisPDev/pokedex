<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;

class IaController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Identify Pokemon from uploaded image.
     */
    public function identify(Request $request)
    {
        $request->validate([
            'image' => 'required', // Can be file or base64
        ]);

        $base64Image = '';
        $mimeType = 'image/jpeg';

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $mimeType = $file->getMimeType();
            $base64Image = base64_encode(file_get_contents($file->getRealPath()));
        } else {
            // Assume base64 string
            $base64Image = $request->image;
        }

        $result = $this->geminiService->identifyPokemon($base64Image, $mimeType);

        return response()->json($result);
    }

    /**
     * Get strategic advisor insights based on user's current collection.
     */
    public function insights(Request $request)
    {
        $collection = $request->user()->pokemonCollections()->get()->toArray();
        $insights = $this->geminiService->getCollectionInsights($collection);

        return response()->json([
            'insights' => $insights
        ]);
    }
}
