<?php

use App\Http\Controllers\Api\ChatSessionController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:20,1')->group(function () {
    Route::post('chat-sessions', [ChatSessionController::class, 'store'])
        ->name('chat-sessions.store');

    Route::post('chat-sessions/{token}/messages', [MessageController::class, 'store'])
        ->name('chat-sessions.messages.store');

    Route::post('chat-sessions/{token}/messages/stream', [MessageController::class, 'stream'])
        ->name('chat-sessions.messages.stream');

    Route::get('chat-sessions/{token}/messages', [MessageController::class, 'index'])
        ->name('chat-sessions.messages.index');
});
