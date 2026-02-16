<?php

namespace App\Http\Controllers;

use App\Services\GroqService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getChatPage()
    {
        return view('chat.chat', [
            'aiModelDefault' => config('ai.groq_default_model')
        ]);
    }

    public function handleRequest(Request $request, GroqService $aiService)
    {
        return $aiService->chat($request->ai_model, $request->ai_role, $request->user_message);
    }
}
