<?php

namespace App\Http\Controllers;

use App\Models\LabRequest;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LabController extends Controller
{
    // ── Lab Dashboard ──────────────────────────────────────────────────
    public function index(): View
    {
        $user = Auth::user();

        $stats = [
            'pending_tests'    => LabRequest::pending()->count(),
            'completed_today'  => LabRequest::completed()
                ->whereDate('completed_at', today())
                ->count(),
            'critical_results' => LabRequest::where('result_flag', 'critical')->count(),
            'total_patients'   => LabRequest::distinct('patient_id')->count('patient_id'),
        ];

        $recentRequests = LabRequest::with(['patient', 'requestedBy'])
            ->latest()
            ->take(10)
            ->get();

        return view('lab.dashboard', compact('user', 'stats', 'recentRequests'));
    }

    // ── All requests list ──────────────────────────────────────────────
    public function requests(Request $request): View
    {
        $requests = LabRequest::with(['patient', 'requestedBy'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) => $q->whereHas('patient', fn($q2) =>
            $q2->where('name', 'like', "%{$request->search}%")
            ))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('lab.requests', compact('requests'));
    }

    // ── Create form (doctor/nurse fills this out) ──────────────────────
    public function create(Patient $patient): View
    {
        return view('lab.create', compact('patient'));
    }

    // ── Store new lab request ──────────────────────────────────────────
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'test_name'     => 'required|string|max:255',
            'clinical_notes' => 'nullable|string',
        ]);

        $patient->labRequests()->create([
            ...$validated,
            'requested_by' => Auth::id(),
            'status'       => 'pending',
        ]);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Lab request submitted successfully.');
    }

    // ── Show single request ────────────────────────────────────────────
    public function show(LabRequest $labRequest): View
    {
        $labRequest->load(['patient', 'requestedBy', 'assignedTo']);
        return view('lab.show', compact('labRequest'));
    }

    // ── Mark as in progress ────────────────────────────────────────────
    public function startTest(LabRequest $labRequest): RedirectResponse
    {
        $labRequest->update([
            'status'      => 'in_progress',
            'assigned_to' => Auth::id(),
        ]);

        return back()->with('success', 'Test marked as in progress.');
    }

    // ── Submit results ─────────────────────────────────────────────────
    public function submitResults(Request $request, LabRequest $labRequest): RedirectResponse
    {
        $validated = $request->validate([
            'results'        => 'required|string',
            'interpretation' => 'nullable|string',
            'result_flag'    => 'required|in:normal,abnormal,critical',
        ]);

        $labRequest->update([
            'results'        => $validated['results'],
            'interpretation' => $validated['interpretation'] ?? null,
            'result_flag'    => $validated['result_flag'],
            'status'         => 'completed',
            'assigned_to'    => Auth::id(),
            'completed_at'   => now(),
        ]);

        return redirect()
            ->route('lab.requests')
            ->with('success', 'Results submitted successfully.');
    }
}
