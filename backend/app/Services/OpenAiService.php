<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY', '');
        $this->baseUrl = 'https://api.openai.com/v1/chat/completions';
    }

    /**
     * Identify a Pokemon from a base64 encoded image using GPT-4o-mini with Vision.
     */
    public function identifyPokemon(string $base64Image, string $mimeType = 'image/jpeg'): array
    {
        if (empty($this->apiKey) || $this->apiKey === 'tu_api_key_aqui') {
            return [
                'name' => 'Desconocido',
                'confidence' => 0.0,
                'type' => 'desconocido',
                'suggestion' => 'Por favor configura tu OPENAI_API_KEY en el archivo .env para habilitar el reconocimiento por IA.',
            ];
        }

        // Clean base64 data if it contains the data prefix (e.g. data:image/jpeg;base64,...)
        if (preg_match('/^data:([^;]+);base64,(.+)$/', $base64Image, $matches)) {
            $mimeType = $matches[1];
            $base64Image = $matches[2];
        }

        try {
            $response = Http::withToken($this->apiKey)->post($this->baseUrl, [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => "Identifica este Pokémon a partir de la imagen proporcionada. Devuelve únicamente un objeto JSON válido con las siguientes claves: 'name' (nombre del Pokémon en minúsculas y formato correcto de PokéAPI), 'confidence' (un flotante de 0.0 a 1.0 representando la confianza), 'type' (el tipo o tipos separados por coma, ej. 'fire, flying'), 'suggestion' (una sugerencia corta de 1 o 2 líneas en español explicando por qué agregarlo o cómo usarlo en batallas). Asegúrate de no incluir bloques de código de markdown como ```json o texto adicional fuera del JSON."
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$base64Image}"
                                ]
                            ]
                        ]
                    ]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->successful()) {
                $resultText = $response->json()['choices'][0]['message']['content'] ?? '{}';
                return json_decode(trim($resultText), true) ?? [];
            } else {
                Log::error('OpenAI API Error: ' . $response->body());
                throw new \Exception('Error de API OpenAI: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('OpenAi Service Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get contextual insights based on the user's collection of Pokemon.
     */
    public function getCollectionInsights(array $collection): string
    {
        if (empty($this->apiKey) || $this->apiKey === 'tu_api_key_aqui') {
            throw new \Exception('OpenAI API Key no configurada.');
        }

        if (empty($collection)) {
            return '¡Tu colección está vacía! Comienza agregando algunos Pokémon favoritos y vuelve a preguntarme para darte consejos estratégicos de equipo.';
        }

        // Format the collection data for the prompt
        $formattedList = array_map(function ($item) {
            return "- {$item['pokemon_name']} (Tipos: {$item['pokemon_type']}) - Notas: " . ($item['custom_notes'] ?? 'Ninguna');
        }, $collection);
        $pokemonListString = implode("\n", $formattedList);

        $prompt = "Actúa como un Profesor Pokémon y experto estratega de batallas. El usuario tiene los siguientes Pokémon en su colección personal:\n\n" .
            $pokemonListString . "\n\n" .
            "Analiza detalladamente esta colección. Proporciona recomendaciones estratégicas y divertidas sobre el balance de tipos, fortalezas, debilidades, y qué tipos de Pokémon le convendría buscar a continuación para complementar su equipo. Escribe tu respuesta en un tono amigable, entusiasta y en español. Mantén el texto corto (máximo 3 párrafos).";

        try {
            $response = Http::withToken($this->apiKey)->post($this->baseUrl, [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'] ?? 'No se pudo generar respuesta.';
            } else {
                Log::error('OpenAI API Insights Error: ' . $response->body());
                throw new \Exception('Error de API OpenAI Insights: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('OpenAI Service Insights Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check text content using OpenAI Moderation API.
     * Returns true if text is appropriate/safe, false if flagged as inappropriate.
     */
    public function checkTextModeration(string $text): bool
    {
        if (empty($this->apiKey) || $this->apiKey === 'tu_api_key_aqui' || empty(trim($text))) {
            return true; 
        }

        try {
            $response = Http::withToken($this->apiKey)->post('https://api.openai.com/v1/moderations', [
                'input' => $text,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $flagged = $result['results'][0]['flagged'] ?? false;
                return !$flagged; 
            } else {
                Log::error('OpenAI Moderation API Error: ' . $response->body());
                throw new \Exception('Error de API OpenAI Moderation: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('OpenAI Moderation Service Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
