<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntentLog extends Model
{
    protected $fillable = [
        'chat_session_id',
        'conversation_id',
        'user_message',
        'detected_intent',
        'tool_called',
        'confidence',
    ];

    protected $casts = [
        'confidence' => 'float',
    ];

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }
}
