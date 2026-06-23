<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    protected $fillable = [
        'token', 'conversation_id', 'last_intent', 'last_confidence_pct',
    ];
}
