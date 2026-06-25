<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\Concerns\AttachesSource;
use App\Models\AidProgram;
use App\Models\DisasterEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AidProgramController extends Controller
{
    use AttachesSource;

    public function index(): Response
    {
        return Inertia::render('portal/aid/Index', [
            'programs' => AidProgram::with(['disasterEvent:id,name', 'updatedBy:id,name'])
                ->latest('id')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('portal/aid/Form', [
            'events' => DisasterEvent::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $program = AidProgram::create([
            ...$data['program'],
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);
        $this->attachSource($program, $data['source']);

        return redirect()->route('portal.aid.index')->with('status', 'Program bantuan ditambahkan.');
    }

    public function edit(AidProgram $aid): Response
    {
        return Inertia::render('portal/aid/Form', [
            'program' => $aid->load('sources:id,name,url,published_at'),
            'events' => DisasterEvent::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, AidProgram $aid): RedirectResponse
    {
        $data = $this->validateData($request);

        $aid->update([
            ...$data['program'],
            'updated_by' => $request->user()->id,
        ]);
        $this->attachSource($aid, $data['source']);

        return redirect()->route('portal.aid.index')->with('status', 'Program bantuan diperbarui.');
    }

    public function destroy(AidProgram $aid): RedirectResponse
    {
        $aid->delete();

        return back()->with('status', 'Program bantuan dihapus.');
    }

    /**
     * @return array{program: array<string, mixed>, source: array<string, mixed>}
     */
    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'aid_type' => ['required', 'in:cash,food,logistics,health,shelter'],
            'description' => ['required', 'string'],
            'region' => ['required', 'string', 'max:255'],
            'eligibility' => ['nullable', 'string'],
            'schedule_status' => ['required', 'in:planned,ongoing,distributed,closed'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'disaster_event_id' => ['nullable', 'exists:disaster_events,id'],
            'is_active' => ['boolean'],
            'source_name' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url'],
            'source_date' => ['nullable', 'date'],
        ]);

        return [
            'program' => collect($validated)->except(['source_name', 'source_url', 'source_date'])->all(),
            'source' => ['name' => $validated['source_name'] ?? null, 'url' => $validated['source_url'] ?? null, 'date' => $validated['source_date'] ?? null],
        ];
    }
}
