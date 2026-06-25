<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\AidProgram;
use App\Models\ClaimVerification;
use App\Models\IntentLog;
use App\Models\ShelterLocation;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('portal/Dashboard', [
            'stats' => [
                'shelters' => ShelterLocation::count(),
                'aid_programs' => AidProgram::count(),
                'claims' => ClaimVerification::count(),
                'pending_reviews' => IntentLog::where('needs_review', true)->count(),
            ],
            'recentReviews' => IntentLog::where('needs_review', true)
                ->latest('id')
                ->limit(5)
                ->get(['id', 'user_message', 'detected_intent', 'created_at']),
        ]);
    }
}
