<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\Concerns\AttachesSource;
use App\Models\ClaimVerification;
use App\Models\DisasterEvent;
use App\Services\ClaimIndexer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ClaimVerificationController extends Controller
{
    use AttachesSource;

    public function __construct(private readonly ClaimIndexer $indexer) {}

    public function index(): Response
    {
        return Inertia::render('portal/claims/Index', [
            'claims' => ClaimVerification::with(['updatedBy:id,name'])
                ->latest('id')
                ->get(['id', 'claim_text', 'status', 'region', 'created_by', 'updated_by', 'updated_at']),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('portal/claims/Form', [
            'events' => DisasterEvent::orderBy('name')->get(['id', 'name']),
            // Prefilled from the review queue ("Tambahkan data resmi").
            'prefill' => $request->query('claim'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $claim = ClaimVerification::create([
            ...$data['claim'],
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);
        $this->attachSource($claim, $data['source']);
        $this->reindex($claim);

        return redirect()->route('portal.claims.index')->with('status', 'Klaim terverifikasi ditambahkan & disinkronkan ke chat.');
    }

    public function edit(ClaimVerification $claim): Response
    {
        return Inertia::render('portal/claims/Form', [
            'claim' => $claim->load('sources:id,name,url,published_at'),
            'events' => DisasterEvent::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, ClaimVerification $claim): RedirectResponse
    {
        $data = $this->validateData($request);

        $claim->update([
            ...$data['claim'],
            'updated_by' => $request->user()->id,
        ]);
        $this->attachSource($claim, $data['source']);
        $this->reindex($claim);

        return redirect()->route('portal.claims.index')->with('status', 'Klaim diperbarui & disinkronkan ke chat.');
    }

    public function destroy(ClaimVerification $claim): RedirectResponse
    {
        $claim->delete();

        return back()->with('status', 'Klaim dihapus.');
    }

    /**
     * Generate the embedding so the new/updated claim is immediately retrievable
     * by the verify_claim tool. Failures are logged but never block the save.
     */
    private function reindex(ClaimVerification $claim): void
    {
        try {
            $this->indexer->index($claim);
        } catch (Throwable $e) {
            Log::warning('portal.claim.reindex_failed', ['id' => $claim->id, 'error' => $e->getMessage()]);
        }
    }

    /**
     * @return array{claim: array<string, mixed>, source: array<string, mixed>}
     */
    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'claim_text' => ['required', 'string'],
            'status' => ['required', 'in:verified,unverified,hoax,no_official_data'],
            'explanation' => ['required', 'string'],
            'region' => ['nullable', 'string', 'max:255'],
            'disaster_event_id' => ['nullable', 'exists:disaster_events,id'],
            'is_active' => ['boolean'],
            // Source is mandatory for fact-checks (grounding principle).
            'source_name' => ['required', 'string', 'max:255'],
            'source_url' => ['required', 'url'],
            'source_date' => ['nullable', 'date'],
        ]);

        return [
            'claim' => collect($validated)->except(['source_name', 'source_url', 'source_date'])->all(),
            'source' => ['name' => $validated['source_name'], 'url' => $validated['source_url'], 'date' => $validated['source_date'] ?? null],
        ];
    }
}
