<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\RadarService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RadarController extends Controller
{
    public function __construct(private readonly RadarService $radar) {}

    /**
     * Radar Tren dashboard: aggregated hoax-claim and per-region need signals.
     */
    public function index(Request $request): Response
    {
        $days = (int) $request->integer('days', 7);
        $days = in_array($days, [7, 14, 30], true) ? $days : 7;

        // 'all' shows live + simulated demo rows together (clearly labelled in UI),
        // 'live' hides demo data, 'simulated' shows only the seeded demo set.
        $source = $request->string('source', 'all')->toString();
        $source = in_array($source, ['all', 'live', 'simulated'], true) ? $source : 'all';

        return Inertia::render('portal/Radar', [
            'filters' => ['days' => $days, 'source' => $source],
            'meta' => $this->radar->meta($days, $source),
            'claimTrends' => $this->radar->claimTrends($days, $source),
            'regionNeeds' => $this->radar->regionNeeds($days, $source),
        ]);
    }
}
