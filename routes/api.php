<?php

use Illuminate\Support\Facades\Route;

use function Laravel\Ai\agent;

Route::get('/test-ai', function () {
    $response = agent(instructions: 'You are a helpful assistant. Reply briefly.')
        ->prompt('Balas dengan: "Cekarah AI siap beroperasi."');

    return response()->json([
        'status' => 'ok',
        'response' => $response->text,
    ]);
})->name('test.ai');
