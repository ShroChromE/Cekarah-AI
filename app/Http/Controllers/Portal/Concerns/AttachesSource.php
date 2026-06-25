<?php

namespace App\Http\Controllers\Portal\Concerns;

use App\Models\Source;
use Illuminate\Database\Eloquent\Model;

trait AttachesSource
{
    /**
     * Find-or-create an official source from volunteer input and attach it to
     * the record via the polymorphic citations relation. Volunteer-entered
     * sources are real (is_simulated = false).
     *
     * @param  array{name?: string|null, url?: string|null, date?: string|null}  $input
     */
    protected function attachSource(Model $record, array $input): void
    {
        if (empty($input['name'])) {
            return;
        }

        $source = Source::firstOrCreate(
            ['name' => $input['name'], 'url' => $input['url'] ?? null],
            ['source_type' => 'official', 'published_at' => $input['date'] ?? null, 'is_simulated' => false],
        );

        $record->sources()->syncWithoutDetaching([$source->id]);
    }
}
