<?php

namespace App\Ai\Support;

use App\Ai\Agents\WebResearchAgent;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Runs a live, Google-Search-grounded research pass in a separate Gemini request
 * (grounding cannot share a request with the main agent's function tools).
 *
 * Failures (overload, timeout) are swallowed and return null so the calling tool
 * degrades gracefully to the structured database data.
 */
class WebResearch
{
    public function research(string $query): ?string
    {
        try {
            $text = trim((new WebResearchAgent)->prompt($query)->text);

            return $text !== '' ? $text : null;
        } catch (Throwable $e) {
            Log::warning('cekarah.web_research.failed', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
