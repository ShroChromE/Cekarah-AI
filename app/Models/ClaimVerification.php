<?php

namespace App\Models;

use App\Models\Concerns\HasCitations;
use Database\Factories\ClaimVerificationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimVerification extends Model
{
    /** @use HasFactory<ClaimVerificationFactory> */
    use HasCitations, HasFactory;

    protected $fillable = [
        'disaster_event_id',
        'claim_text',
        'status',
        'explanation',
        'region',
        'embedding',
        'is_active',
    ];

    protected $casts = [
        'embedding' => 'array',
        'is_active' => 'boolean',
    ];

    public function disasterEvent(): BelongsTo
    {
        return $this->belongsTo(DisasterEvent::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
