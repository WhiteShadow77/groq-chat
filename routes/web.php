<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chat', [ChatController::class, 'getChatPage'])->name('chat');
Route::post('/chat/handle-request', [ChatController::class, 'handleRequest'])->name('chat.handle-request');