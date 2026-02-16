<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GroqService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('ai.groq_api_key');
    }

    /**
     * Send a prompt to the AI and return the text response.
     */
    public function chat(string $aiModel, string $aiRole, string $userMessage): JsonResponse
    {
        $response = Http::withToken($this->apiKey)
            ->accept('application/json')
            ->post($this->baseUrl, [
                'model' => $aiModel,
                'messages' => [
                    ['role' => 'system', 'content' => $aiRole],
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

        if ($response->failed()) {
            $httpCode = $response->status();

            // If code is 0 or not a valid HTTP error code, default to 400 (Bad Request)
            if ($httpCode < 400 || $httpCode > 599) {
                $httpCode = 400;
            }

            return response()->json([
                'status' => false,
                'message' => 'Error occurred',
                'data' => [
                    'message' => $response->json()['error']['message']
                ]
            ], $httpCode);

        }
        return response()->json([
            'status' => true,
            'message' => 'Answer from AI',
            'data' => [
                'answer' => $response->json('choices.0.message.content')
            ]
        ]);

    }
}