<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ChatSessionController extends Controller
{
    public function store(): JsonResponse
    {
        $session = ChatSession::create([
            'token' => Str::random(40),
        ]);

        return response()->json([
            'token' => $session->token,
            'created_at' => $session->created_at->toIso8601String(),
        ], 201);
    }
}
