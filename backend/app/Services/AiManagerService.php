<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AiManagerService
{
    protected OpenAiService $openAiService;
    protected GeminiService $geminiService;

    public function __construct(OpenAiService $openAiService, GeminiService $geminiService)
    {
        $this->openAiService = $openAiService;
        $this->geminiService = $geminiService;
    }

    /**
     * Check text moderation with fallback support.
     */
    public function checkTextModeration(string $text): bool
    {
        try {
            // Intentar con OpenAI
            return $this->openAiService->checkTextModeration($text);
        } catch (\Exception $e) {
            Log::warning('OpenAI Moderation falló, aplicando fallback a Gemini. Detalle: ' . $e->getMessage());
            
            // Fallback a Gemini
            return $this->geminiService->checkTextModeration($text);
        }
    }

    /**
     * Identify Pokemon with fallback support.
     */
    public function identifyPokemon(string $base64Image, string $mimeType = 'image/jpeg'): array
    {
        try {
            // Intentar con OpenAI
            $result = $this->openAiService->identifyPokemon($base64Image, $mimeType);
            
            // Si retorna el resultado genérico de error de OpenAI, forzamos excepción para ir a fallback
            if (isset($result['name']) && $result['name'] === 'Error') {
                throw new \Exception('OpenAI retornó una estructura de error en la identificación.');
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::warning('OpenAI Vision falló, aplicando fallback a Gemini. Detalle: ' . $e->getMessage());
            
            // Fallback a Gemini
            return $this->geminiService->identifyPokemon($base64Image, $mimeType);
        }
    }

    /**
     * Get Collection Insights with fallback support.
     */
    public function getCollectionInsights(array $collection): string
    {
        try {
            // Intentar con OpenAI
            $insights = $this->openAiService->getCollectionInsights($collection);
            
            // Validar que no hayamos retornado el string de error de OpenAI
            if (str_contains($insights, 'no pude contactar al Profesor') || str_contains($insights, 'Revisa tus logs')) {
                throw new \Exception('OpenAI falló al generar consejos del Profesor Pokémon.');
            }
            
            return $insights;
        } catch (\Exception $e) {
            Log::warning('OpenAI Insights falló, aplicando fallback a Gemini. Detalle: ' . $e->getMessage());
            
            // Fallback a Gemini
            return $this->geminiService->getCollectionInsights($collection);
        }
    }
}
