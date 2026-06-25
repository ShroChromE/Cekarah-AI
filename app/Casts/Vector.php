<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Casts a pgvector column to/from a PHP float array.
 *
 * - get: parses the "[0.1,0.2,...]" text pgvector returns into array<float>.
 * - set: serializes an array<float> into the "[...]" literal pgvector expects.
 *   A string is passed through untouched (already a vector literal).
 *
 * @implements CastsAttributes<array<int, float>|null, array<int, float>|string|null>
 */
class Vector implements CastsAttributes
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return array<int, float>|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        return array_map('floatval', explode(',', trim((string) $value, '[]')));
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, string|null>
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value === null) {
            return [$key => null];
        }

        if (is_string($value)) {
            return [$key => $value];
        }

        return [$key => '['.implode(',', $value).']'];
    }
}
