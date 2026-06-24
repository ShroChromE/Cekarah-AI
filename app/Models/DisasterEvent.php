<?php

namespace App\Models;

use App\Models\Concerns\HasCitations;
use Database\Factories\DisasterEventFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisasterEvent extends Model
{
    /** @use HasFactory<DisasterEventFactory> */
    use HasCitations, HasFactory;

    protected $fillable = [
        'name',
        'type',
        'region',
        'province',
        'status',
        'severity',
        'started_at',
        'description',
        'latitude',
        'longitude',
        'embedding',
        'is_active',
    ];

    protected $casts = [
        'started_at' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
        'embedding' => 'array',
        'is_active' => 'boolean',
    ];

    public function claimVerifications(): HasMany
    {
        return $this->hasMany(ClaimVerification::class);
    }

    public function shelterLocations(): HasMany
    {
        return $this->hasMany(ShelterLocation::class);
    }

    public function aidPrograms(): HasMany
    {
        return $this->hasMany(AidProgram::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
