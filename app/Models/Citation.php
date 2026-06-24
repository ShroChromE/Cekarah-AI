<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Citation extends Model
{
    protected $fillable = [
        'source_id',
        'citable_type',
        'citable_id',
        'quote',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function citable(): MorphTo
    {
        return $this->morphTo();
    }
}
