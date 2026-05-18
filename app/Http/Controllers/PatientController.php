<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientNote;
use App\Models\Prescription;
use App\Models\StockItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────────

    public function dashboard()
    {
        $stats = [
            'inpatients'       => Patient::admitted()->count(),
            'total_patients'   => Patient::count(),
            'discharged_today' => Patient::whereDate('discharged_at', today())->count(),
            'low_stock'        => StockItem::lowStock()->count(),
        ];

        $recentPatients = Patient::admitted()
            ->latest('admitted_at')
            ->take(8)
            ->get();

        // ← Add this: patients admitted or updated today
        $todaysPatients = Patient::whereDate('admitted_at', today())
            ->orWhereDate('updated_at', today())
            ->latest()
            ->take(10)
            ->get();

        $lowStock = StockItem::lowStock()
            ->orderBy('quantity')
            ->take(5)
            ->get();

        $statusCounts = [
            'stable'      => Patient::admitted()->where('status', 'stable')->count(),
            'critical'    => Patient::admitted()->where('status', 'critical')->count(),
            'recovering'  => Patient::admitted()->where('status', 'recovering')->count(),
            'observation' => Patient::admitted()->where('status', 'observation')->count(),
        ];

        return view('medical.dashboard', compact(
            'stats', 'recentPatients', 'todaysPatients', 'lowStock', 'statusCounts'
        ));
    }
    // ── Patient CRUD ──────────────────────────────────────────────────

    public function index(Request $request)
    {
        $patients = Patient::query()
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'date_of_birth'           => 'required|date|before:today',
            'gender'                  => 'required|in:male,female,other',
            'phone'                   => 'nullable|string|max:20',
            'address'                 => 'nullable|string|max:500',
            'ward'                    => 'nullable|string|max:100',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'diagnosis'               => 'required|string',
            'status'                  => 'nullable|in:admitted,observation,critical',
        ]);

        $validated['admitted_at'] = now();
        $validated['status']      = $validated['status'] ?? 'admitted';

        $patient = Patient::create($validated);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', "Patient {$patient->name} admitted successfully.");
    }

    public function show(Patient $patient)
    {
        $patient->load(['clinicalNotes.user', 'prescriptions.stockItem', 'prescriptions.prescribedBy']);
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'age'               => 'required|integer|min:0|max:150',
            'gender'            => 'required|in:male,female,other',
            'phone'             => 'nullable|string|max:20',
            'address'           => 'nullable|string|max:500',
            'ward'              => 'nullable|string|max:100',
            'emergency_contact' => 'nullable|string|max:255',
            'diagnosis'         => 'nullable|string',
            'status'            => 'nullable|in:admitted,stable,observation,critical,recovering,discharged',
        ]);

        $patient->update($validated);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient record updated successfully.');
    }

    // ── Admissions & Discharges ───────────────────────────────────────

    public function admission(Request $request)
    {
        $patients = Patient::admitted()
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->ward, fn($q) => $q->where('ward', $request->ward))
            ->latest('admitted_at')
            ->paginate(12)
            ->withQueryString();

        $statusCounts = [
            'stable'      => Patient::admitted()->where('status', 'stable')->count(),
            'critical'    => Patient::admitted()->where('status', 'critical')->count(),
            'recovering'  => Patient::admitted()->where('status', 'recovering')->count(),
            'observation' => Patient::admitted()->where('status', 'observation')->count(),
        ];

        return view('patients.admission', compact('patients', 'statusCounts'));
    }

    public function discharge()
    {
        $patients = Patient::where('status', 'discharged')
            ->latest('discharged_at')
            ->paginate(20);

        return view('patients.discharge', compact('patients'));
    }

    public function dischargeForm(Patient $patient)
    {
        if ($patient->status === 'discharged') {
            return redirect()
                ->route('patients.show', $patient)
                ->with('error', 'This patient has already been discharged.');
        }

        return view('patients.discharge_form', compact('patient'));
    }

    public function dischargeSubmit(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'discharge_condition' => 'required|in:recovered,improved,transferred,self-discharge,deceased',
            'discharge_notes'     => 'required|string',
            'followup_date'       => 'nullable|date|after:today',
        ]);

        $patient->update([
            'status'               => 'discharged',
            'discharge_condition'  => $validated['discharge_condition'],
            'discharge_notes'      => $validated['discharge_notes'],
            'followup_date'        => $validated['followup_date'] ?? null,
            'discharged_at'        => now(),
        ]);

        return redirect()
            ->route('patients.discharge')
            ->with('success', "{$patient->name} has been discharged successfully.");
    }

    // ── Clinical Notes ────────────────────────────────────────────────

    public function addNote(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'type'    => 'nullable|in:progress,assessment,observation,procedure',
        ]);

        $patient->clinicalNotes()->create([  // ← changed from notes()
            'content' => $validated['content'],
            'type'    => $validated['type'] ?? 'progress',
            'user_id' => auth()->id(),
        ]);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Clinical note added.');
    }

    // ── Prescriptions ─────────────────────────────────────────────────

    public function addPrescription(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'stock_item_id' => 'required|exists:stock_items,id',
            'dosage'        => 'required|string|max:100',
            'frequency'     => 'required|string|max:100',
            'duration'      => 'nullable|string|max:100',
            'quantity'      => 'nullable|integer|min:1',
            'instructions'  => 'nullable|string',
        ]);

        $stockItem = StockItem::findOrFail($validated['stock_item_id']);

        if ($stockItem->quantity < 1) {
            return back()->with('error', "'{$stockItem->name}' is out of stock.");
        }

        $patient->prescriptions()->create([
            'stock_item_id'   => $validated['stock_item_id'],
            'dosage'          => $validated['dosage'],
            'frequency'       => $validated['frequency'],
            'duration'        => $validated['duration'] ?? null,
            'quantity'        => $validated['quantity'] ?? null,
            'instructions'    => $validated['instructions'] ?? null,
            'prescribed_by'   => auth()->id(),
        ]);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Prescription issued successfully.');
    }
}
