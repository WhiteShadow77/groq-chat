<?php

namespace App\Http\Controllers;

use App\Services\GroqService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getChatPage()
    {
        return view('chat.chat', [
            'aiModelDefault' => config('ai.modelDefault')
        ]);
    }

    public function handleRequest(Request $request, GroqService $aiService)
    {
        $answer = $aiService->chat($request->ai_model, $request->ai_role, $request->user_message);

        return response()->json([
            'status' => true,
            'message' => 'Answer from AI',
            'data' => [
                'answer' => $answer
            ]
        ]);
    }
}
