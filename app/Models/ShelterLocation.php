<?php

namespace App\Models;

use App\Models\Concerns\HasCitations;
use Database\Factories\ShelterLocationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShelterLocation extends Model
{
    /** @use HasFactory<ShelterLocationFactory> */
    use HasCitations, HasFactory;

    protected $fillable = [
        'disaster_event_id',
        'name',
        'type',
        'address',
        'region',
        'latitude',
        'longitude',
        'capacity',
        'occupancy',
        'contact',
        'notes',
        'embedding',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'capacity' => 'integer',
        'occupancy' => 'integer',
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
