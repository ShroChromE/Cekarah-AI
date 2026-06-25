<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\Concerns\AttachesSource;
use App\Models\DisasterEvent;
use App\Models\ShelterLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShelterLocationController extends Controller
{
    use AttachesSource;

    public function index(): Response
    {
        return Inertia::render('portal/shelters/Index', [
            'shelters' => ShelterLocation::with(['disasterEvent:id,name', 'updatedBy:id,name'])
                ->latest('id')
                ->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('portal/shelters/Form', [
            'events' => DisasterEvent::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $shelter = ShelterLocation::create([
            ...$data['shelter'],
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);
        $this->attachSource($shelter, $data['source']);

        return redirect()->route('portal.shelters.index')->with('status', 'Posko ditambahkan.');
    }

    public function edit(ShelterLocation $shelter): Response
    {
        return Inertia::render('portal/shelters/Form', [
            'shelter' => $shelter->load('sources:id,name,url,published_at'),
            'events' => DisasterEvent::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, ShelterLocation $shelter): RedirectResponse
    {
        $data = $this->validateData($request);

        $shelter->update([
            ...$data['shelter'],
            'updated_by' => $request->user()->id,
        ]);
        $this->attachSource($shelter, $data['source']);

        return redirect()->route('portal.shelters.index')->with('status', 'Posko diperbarui.');
    }

    public function destroy(ShelterLocation $shelter): RedirectResponse
    {
        $shelter->delete();

        return back()->with('status', 'Posko dihapus.');
    }

    /**
     * @return array{shelter: array<string, mixed>, source: array<string, mixed>}
     */
    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:evacuation_shelter,public_kitchen,health_post,logistics_post'],
            'address' => ['required', 'string'],
            'region' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'occupancy' => ['nullable', 'integer', 'min:0'],
            'contact' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'disaster_event_id' => ['nullable', 'exists:disaster_events,id'],
            'is_active' => ['boolean'],
            'source_name' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url'],
            'source_date' => ['nullable', 'date'],
        ]);

        return [
            'shelter' => collect($validated)->except(['source_name', 'source_url', 'source_date'])->all(),
            'source' => ['name' => $validated['source_name'] ?? null, 'url' => $validated['source_url'] ?? null, 'date' => $validated['source_date'] ?? null],
        ];
    }
}
