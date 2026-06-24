<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    protected $fillable = [
        'token', 'conversation_id', 'last_intent', 'last_confidence_pct',
    ];

    public function intentLogs(): HasMany
    {
        return $this->hasMany(IntentLog::class);
    }
}
