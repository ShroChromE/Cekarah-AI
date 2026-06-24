<?php

namespace App\Ai\Agents;

use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Laravel\Ai\Providers\Tools\WebSearch;

/**
 * A focused research agent that grounds answers in live Google Search results.
 *
 * Kept separate from CekarahAgent because Gemini's google_search grounding and
 * custom function-calling cannot run in the same request — this agent uses only
 * the provider web-search tool and is invoked from within the data tools.
 */
// Uses gemini-2.5-flash: reliably supports google_search grounding and tends to
// be more available than the preview model. This is an internal research helper,
// separate from the user-facing CekarahAgent voice.
#[Model('gemini-2.5-flash')]
#[Timeout(30)]
class WebResearchAgent implements Agent, HasTools
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
Kamu adalah peneliti informasi bencana untuk Indonesia. Cari informasi TERKINI dari
internet, utamakan sumber resmi (bnpb.go.id, bmkg.go.id, BPBD daerah, kemensos.go.id,
pmi.or.id) dan media kredibel.

Aturan:
- Ringkas temuan secara faktual dalam Bahasa Indonesia.
- WAJIB sertakan nama sumber + URL + tanggal untuk setiap fakta penting.
- Jika tidak menemukan informasi resmi yang relevan, katakan jujur "tidak ditemukan
  data resmi terkini" — jangan mengarang.
INSTRUCTIONS;
    }

    /**
     * @return array<int, object>
     */
    public function tools(): iterable
    {
        return [
            (new WebSearch)->max(3),
        ];
    }
}
