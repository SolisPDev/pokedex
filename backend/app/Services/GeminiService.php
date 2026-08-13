<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
        // Usamos la API v1beta que sí tiene gemini-1.5-flash habilitado para generateContent.
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';
    }

    /**
     * Identify a Pokemon from a base64 encoded image using Gemini 1.5 Flash.
     */
    public function identifyPokemon(string $base64Image, string $mimeType = 'image/jpeg'): array
    {
        if (empty($this->apiKey) || $this->apiKey === 'tu_api_key_de_gemini_aqui') {
            return [
                'name' => 'Desconocido',
                'confidence' => 0.0,
                'type' => 'desconocido',
                'suggestion' => 'Configura GEMINI_API_KEY en el archivo .env.',
            ];
        }

        // Limpiar prefijo base64 si existe
        if (preg_match('/^data:([^;]+);base64,(.+)$/', $base64Image, $matches)) {
            $mimeType = $matches[1];
            $base64Image = $matches[2];
        }

        $url = "{$this->baseUrl}/gemini-3.5-flash:generateContent?key={$this->apiKey}";

        try {
            $response = Http::post($url, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => "Identifica este Pokémon a partir de la imagen. Devuelve únicamente un objeto JSON válido con las siguientes claves exactas: 'name' (nombre del Pokémon en minúsculas y formato correcto de PokéAPI), 'confidence' (un flotante de 0.0 a 1.0 representando la confianza), 'type' (el tipo o tipos separados por coma, ej. 'fire, flying'), 'suggestion' (una sugerencia corta de 1 o 2 líneas en español explicando por qué agregarlo o cómo usarlo en batallas). Asegúrate de no incluir bloques de código de markdown como ```json o texto adicional fuera del JSON."
                            ],
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeType,
                                    'data' => $base64Image
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $candidates = $response->json()['candidates'] ?? [];
                $resultText = $candidates[0]['content']['parts'][0]['text'] ?? '{}';
                return json_decode(trim($resultText), true) ?? [];
            } else {
                Log::error('Gemini API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Gemini Service Exception: ' . $e->getMessage());
        }

        return [
            'name' => 'Error',
            'confidence' => 0.0,
            'type' => 'error',
            'suggestion' => 'Hubo un problema al procesar la imagen con la IA.',
        ];
    }

    /**
     * Get strategic advisor insights based on the user's collection of Pokemon.
     */
    public function getCollectionInsights(array $collection): string
    {
        if (empty($this->apiKey) || $this->apiKey === 'tu_api_key_de_gemini_aqui') {
            return 'Configura GEMINI_API_KEY en el archivo .env para habilitar los consejos del asistente de IA.';
        }

        if (empty($collection)) {
            return '¡Tu colección está vacía! Comienza agregando algunos Pokémon favoritos y vuelve a preguntarme para darte consejos estratégicos de equipo.';
        }

        $formattedList = array_map(function ($item) {
            return "- {$item['pokemon_name']} (Tipos: {$item['pokemon_type']}) - Notas: " . ($item['custom_notes'] ?? 'Ninguna');
        }, $collection);
        $pokemonListString = implode("\n", $formattedList);

        $prompt = "Actúa como un Profesor Pokémon y experto estratega de batallas. El usuario tiene los siguientes Pokémon en su colección personal:\n\n" .
            $pokemonListString . "\n\n" .
            "Analiza detalladamente esta colección. Proporciona recomendaciones estratégicas y divertidas sobre el balance de tipos, fortalezas, debilidades, y qué tipos de Pokémon le convendría buscar a continuación para complementar su equipo. Escribe tu respuesta en un tono amigable, entusiasta y en español. Mantén el texto corto (máximo 3 párrafos).";

        $url = "{$this->baseUrl}/gemini-3.5-flash:generateContent?key={$this->apiKey}";

        try {
            $response = Http::post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $candidates = $response->json()['candidates'] ?? [];
                return $candidates[0]['content']['parts'][0]['text'] ?? 'No se pudo generar respuesta.';
            } else {
                Log::error('Gemini API Insights Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Gemini Service Insights Exception: ' . $e->getMessage());
        }

        return 'Lo siento, no pude contactar al Profesor Pokémon en este momento. Revisa tus logs.';
    }

    /**
     * Check text content for moderation. Returns true if safe, false if inappropriate.
     * We will use a fast classification prompt on Gemini to match OpenAI Moderation API capability.
     */
    public function checkTextModeration(string $text): bool
    {
        if (empty($this->apiKey) || $this->apiKey === 'tu_api_key_de_gemini_aqui' || empty(trim($text))) {
            return true;
        }

        $url = "{$this->baseUrl}/gemini-3.5-flash:generateContent?key={$this->apiKey}";

        $systemInstruction = "Analiza el siguiente texto y determina si contiene lenguaje inapropiado, insultos, contenido ofensivo, odio, acoso o violencia. Devuelve únicamente un objeto JSON con una sola clave 'flagged' cuyo valor sea true o false (ejemplo: {\"flagged\": true} o {\"flagged\": false}). No agregues más texto ni formato markdown.";

        try {
            $response = Http::post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => "Instrucción: {$systemInstruction}\n\nTexto a analizar: \"{$text}\""]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $candidates = $response->json()['candidates'] ?? [];
                $resultText = $candidates[0]['content']['parts'][0]['text'] ?? '{"flagged":false}';
                $resultJson = json_decode(trim($resultText), true);
                $flagged = $resultJson['flagged'] ?? false;
                return !$flagged; // Retorna true si NO está marcado (flagged = false)
            } else {
                Log::error('Gemini Moderation API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Gemini Moderation Service Exception: ' . $e->getMessage());
        }

        return true; // Ante cualquier fallo dejamos pasar
    }
}
