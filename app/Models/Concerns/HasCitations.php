<?php

namespace App\Models\Concerns;

use App\Models\Citation;
use App\Models\Source;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Adds polymorphic citation support to a model so any record can reference one
 * or more reusable {@see Source} entries (name, url, date) shown to the user.
 */
trait HasCitations
{
    public function citations(): MorphMany
    {
        return $this->morphMany(Citation::class, 'citable');
    }

    public function sources(): MorphToMany
    {
        return $this->morphToMany(Source::class, 'citable', 'citations')
            ->withPivot('quote')
            ->withTimestamps();
    }
}
