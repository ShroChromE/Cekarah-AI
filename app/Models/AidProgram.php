<?php

namespace App\Models;

use App\Models\Concerns\HasCitations;
use App\Models\Concerns\TracksCuration;
use Database\Factories\AidProgramFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AidProgram extends Model
{
    /** @use HasFactory<AidProgramFactory> */
    use HasCitations, HasFactory, TracksCuration;

    protected $fillable = [
        'created_by',
        'updated_by',
        'disaster_event_id',
        'name',
        'provider',
        'aid_type',
        'description',
        'region',
        'eligibility',
        'schedule_status',
        'starts_at',
        'ends_at',
        'embedding',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
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
