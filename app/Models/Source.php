<?php

namespace App\Models;

use Database\Factories\SourceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    /** @use HasFactory<SourceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'publisher',
        'source_type',
        'published_at',
        'is_simulated',
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_simulated' => 'boolean',
    ];

    public function citations(): HasMany
    {
        return $this->hasMany(Citation::class);
    }
}
