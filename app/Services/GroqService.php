<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GroqService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('ai.api_key');
    }

    /**
     * Send a prompt to the AI and return the text response.
     */
    public function chat(string $aiModel, string $aiRole, string $userMessage): string
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

        //dd($response->json(), $response->body(), $response);

        if ($response->failed()) {
            //$errorMessage = $response->json()['error']['message'];
            //dd($response->json(), $response->body(), $response);
            throw new \Exception($response->json()['error']['message'],409);
        }
        return $response->json('choices.0.message.content');
    }
}