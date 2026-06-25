<?php

namespace App\Models;

use Database\Factories\IntentLogFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntentLog extends Model
{
    /** @use HasFactory<IntentLogFactory> */
    use HasFactory;

    protected $fillable = [
        'chat_session_id',
        'conversation_id',
        'user_message',
        'detected_intent',
        'region',
        'tool_called',
        'needs_review',
        'is_simulated',
        'confidence',
    ];

    protected $casts = [
        'needs_review' => 'boolean',
        'is_simulated' => 'boolean',
        'confidence' => 'float',
    ];

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    /**
     * Limit to one of the intent categories powering the radar.
     *
     * @param  array<int, string>  $intents
     */
    public function scopeWhereIntentIn(Builder $query, array $intents): void
    {
        $query->whereIn('detected_intent', $intents);
    }
}
