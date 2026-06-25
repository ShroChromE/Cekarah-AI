<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\IntentLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    /**
     * The review queue: chat messages where a tool found no official data.
     */
    public function index(): Response
    {
        return Inertia::render('portal/Review', [
            'items' => IntentLog::where('needs_review', true)
                ->latest('id')
                ->paginate(20)
                ->through(fn (IntentLog $log) => [
                    'id' => $log->id,
                    'user_message' => $log->user_message,
                    'detected_intent' => $log->detected_intent,
                    'created_at' => $log->created_at?->toIso8601String(),
                ]),
        ]);
    }

    /**
     * Mark a review item as resolved (data has been added elsewhere).
     */
    public function resolve(Request $request, IntentLog $log): RedirectResponse
    {
        $log->update(['needs_review' => false]);

        return back()->with('status', 'Item ditandai sudah ditinjau.');
    }
}
