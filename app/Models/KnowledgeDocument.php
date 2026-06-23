<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeDocument extends Model
{
    protected $fillable = [
        'title', 'content', 'source_url', 'source_name',
        'category', 'topic', 'source_date', 'indexed_at', 'is_active',
    ];

    protected $casts = [
        'source_date' => 'datetime',
        'indexed_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function chunks(): HasMany
    {
        return $this->hasMany(KnowledgeChunk::class, 'document_id');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeNeedsIndexing(Builder $query): void
    {
        $query->active()->where(
            fn (Builder $q) => $q->whereNull('indexed_at')
                ->orWhereColumn('updated_at', '>', 'indexed_at')
        );
    }
}
