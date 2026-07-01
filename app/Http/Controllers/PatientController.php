<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Bill;
use App\Models\LabRequest;
use App\Models\MortuaryRecord;
use App\Models\Patient;
use App\Models\PatientNote;
use App\Models\Prescription;
use App\Models\StockItem;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    // ── Ownership guard (private helper) ───────────────────────────────

    private function denyIfNotAttendingDoctor(Patient $patient): ?RedirectResponse
    {
        $user = Auth::user();

        if ($user->role !== 'doctor') {
            return null;
        }

        if ($patient->doctor_id === null || $patient->doctor_id === $user->id) {
            return null;
        }

        return back()->with('error',
            "You're not the attending doctor for {$patient->name}. Only Dr. " .
            ($patient->doctor->name ?? 'the assigned doctor') .
            " can perform this action, unless the patient is reassigned to you."
        );
    }

    // ── Auto Bill Generator (private helper) ──────────────────────────

    private function autoGenerateBill(Patient $patient): void
    {
        if ($patient->bills()->exists()) return;

        // Bed charges only apply to inpatients (patients assigned a bed).
        $hasBed   = ! is_null($patient->bed_id);
        $bedDays  = $hasBed ? max(1, $patient->days_admitted ?? 1) : 0;
        $bedRate  = 10000;
        $bedTotal = $bedDays * $bedRate;

        $consultationFee = 5000; // every patient who sees a doctor pays this
        $labTotal        = $patient->labRequests()->where('status', 'completed')->count() * 5000;
        $drugsTotal      = $patient->prescriptions()->count() * 500;
        $grandTotal      = $consultationFee + $bedTotal + $labTotal + $drugsTotal;

        Bill::create([
            'patient_id'       => $patient->id,
            'created_by'       => Auth::id(),
            'bed_days'         => $bedDays,
            'bed_rate_per_day' => $hasBed ? $bedRate : 0,
            'bed_total'        => $bedTotal,
            'lab_total'        => $labTotal,
            'drugs_total'      => $drugsTotal,
            // Store the consultation fee in extra_charges so it's visible
            // on the bill breakdown without needing a new column.
            'extra_charges'    => [['label' => 'Consultation fee', 'amount' => $consultationFee]],
            'grand_total'      => $grandTotal,
            'amount_paid'      => 0,
            'balance'          => $grandTotal,
            'status'           => 'unpaid',
        ]);
    }

    // ── Dashboard ─────────────────────────────────────────────────────

    public function dashboard()
    {
        $stats = [
            'inpatients'       => Patient::admitted()->count(),
            'total_patients'   => Patient::count(),
            'admitted_today'   => Patient::whereDate('admitted_at', today())->count(),
            'discharged_today' => Patient::whereDate('discharged_at', today())->count(),
            'low_stock'        => StockItem::lowStock()->count(),
        ];

        $recentPatients = Patient::admitted()
            ->latest('admitted_at')
            ->take(8)
            ->get();

        $vitalsPatient = $recentPatients->first();

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

        $wards = Ward::with('beds')->orderBy('name')->get();

        return view('medical.dashboard', compact(
            'stats', 'recentPatients', 'todaysPatients', 'lowStock', 'statusCounts', 'wards', 'vitalsPatient'
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
        $wards = Ward::with('beds')->orderBy('name')->get();

        return view('patients.create', compact('wards'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:255',
            'date_of_birth'           => 'required|date|before:today',
            'gender'                  => 'required|in:male,female,other',
            'phone'                   => 'nullable|string|max:20',
            'address'                 => 'nullable|string|max:500',
            'emergency_contact_name'  => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'diagnosis'               => 'required|string',
            'status'                  => 'nullable|in:admitted,observation,critical',
            'bed_id'                  => 'nullable|exists:beds,id',
        ]);

        $bedId = $validated['bed_id'] ?? null;
        unset($validated['bed_id']);

        $validated['admitted_at'] = now();
        $validated['status']      = $validated['status'] ?? 'admitted';

        $patient = DB::transaction(function () use ($validated, $bedId) {
            $patient = Patient::create($validated);

            if ($bedId) {
                $bed = Bed::where('id', $bedId)
                    ->where('status', 'available')
                    ->lockForUpdate()
                    ->first();

                if ($bed) {
                    $bed->assignPatient($patient);
                }
            }

            return $patient;
        });

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', "Patient {$patient->name} admitted successfully.");
    }

    public function show(Patient $patient)
    {
        $user = Auth::user();
        $isAttendingOrExempt = $user->role !== 'doctor'
            || $patient->doctor_id === null
            || $patient->doctor_id === $user->id;

        if (! $isAttendingOrExempt) {
            return view('patients.show_restricted', compact('patient'));
        }

        $patient->load([
            'clinicalNotes.user',
            'prescriptions.prescribedBy',
            'labRequests.requestedBy',
        ]);

        $bill = $patient->bills()->latest()->first();

        $otherDoctors = User::where('role', 'doctor')
            ->where('id', '!=', $patient->doctor_id)
            ->orderBy('name')
            ->get();

        return view('patients.show', compact('patient', 'bill', 'otherDoctors'));
    }

    public function edit(Patient $patient)
    {
        if ($redirect = $this->denyIfNotAttendingDoctor($patient)) {
            return $redirect;
        }

        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        if ($redirect = $this->denyIfNotAttendingDoctor($patient)) {
            return $redirect;
        }

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'age'               => 'required|integer|min:0|max:150',
            'gender'            => 'required|in:male,female,other',
            'phone'             => 'nullable|string|max:20',
            'address'           => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:255',
            'diagnosis'         => 'nullable|string',
            'status'            => 'nullable|in:admitted,stable,observation,critical,recovering,discharged',
        ]);

        $patient->update($validated);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient record updated successfully.');
    }

    // ── Reassignment ────────────────────────────────────────────────────

    public function reassign(Request $request, Patient $patient): RedirectResponse
    {
        $user = Auth::user();

        if ($user->role === 'doctor' && $patient->doctor_id !== null && $patient->doctor_id !== $user->id) {
            return back()->with('error',
                "Only the attending doctor can reassign {$patient->name} to another doctor."
            );
        }

        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:users,id'],
        ]);

        $newDoctor = User::where('id', $validated['doctor_id'])
            ->where('role', 'doctor')
            ->first();

        if (! $newDoctor) {
            return back()->with('error', 'Selected user is not a valid doctor.');
        }

        $patient->update(['doctor_id' => $newDoctor->id]);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', "{$patient->name} has been reassigned to Dr. {$newDoctor->name}.");
    }

    // ── Admissions & Discharges ───────────────────────────────────────

    public function admission(Request $request)
    {
        $patients = Patient::admitted()
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->ward,   fn($q) => $q->where('ward', $request->ward))
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
        if ($redirect = $this->denyIfNotAttendingDoctor($patient)) {
            return $redirect;
        }

        if ($patient->status === 'discharged') {
            return redirect()
                ->route('patients.show', $patient)
                ->with('error', 'This patient has already been discharged.');
        }

        $patient->load(['bills', 'labRequests', 'prescriptions']);

        return view('patients.discharge_form', compact('patient'));
    }

    public function generateBill(Patient $patient)
    {
        if ($redirect = $this->denyIfNotAttendingDoctor($patient)) {
            return $redirect;
        }

        if ($patient->bills()->exists()) {
            return back()->with('error', 'A bill already exists for this patient.');
        }

        // Bed charges only apply to inpatients (patients assigned a bed).
        $hasBed   = ! is_null($patient->bed_id);
        $bedDays  = $hasBed ? max(1, $patient->days_admitted) : 0;
        $bedRate  = 10000;
        $bedTotal = $bedDays * $bedRate;

        $consultationFee = 5000; // every patient who sees a doctor pays this
        $labTotal        = $patient->labRequests()->where('status', 'completed')->count() * 5000;
        $drugsTotal      = $patient->prescriptions()->count() * 500;
        $grandTotal      = $consultationFee + $bedTotal + $labTotal + $drugsTotal;

        Bill::create([
            'patient_id'       => $patient->id,
            'created_by'       => Auth::id(),
            'bed_days'         => $bedDays,
            'bed_rate_per_day' => $hasBed ? $bedRate : 0,
            'bed_total'        => $bedTotal,
            'lab_total'        => $labTotal,
            'drugs_total'      => $drugsTotal,
            'extra_charges'    => [['label' => 'Consultation fee', 'amount' => $consultationFee]],
            'grand_total'      => $grandTotal,
            'amount_paid'      => 0,
            'balance'          => $grandTotal,
            'status'           => 'unpaid',
        ]);

        return back()->with('success',
            'Bill of TZS ' . number_format($grandTotal) . ' generated and sent to accountant.'
        );
    }

    public function dischargeSubmit(Request $request, Patient $patient): RedirectResponse
    {
        if ($redirect = $this->denyIfNotAttendingDoctor($patient)) {
            return $redirect;
        }

        $validated = $request->validate([
            'discharge_condition' => 'required|in:recovered,improved,transferred,self-discharge,deceased',
            'discharge_notes'     => 'required|string',
            'followup_date'       => 'nullable|date|after:today',
            'cause_of_death'      => 'required_if:discharge_condition,deceased|nullable|string',
            'bed_rate_per_day'    => 'nullable|numeric|min:0',
        ]);

        $this->autoGenerateBill($patient);

        $bill = $patient->bills()->latest()->first();

        if (!$bill || !in_array($bill->status, ['paid', 'waived'])) {
            return redirect()
                ->route('patients.discharge.form', $patient)
                ->with('error', 'Cannot discharge: patient bill has not been paid yet.');
        }

        $patient->update([
            'status'              => $validated['discharge_condition'] === 'deceased' ? 'deceased' : 'discharged',
            'discharge_condition' => $validated['discharge_condition'],
            'discharge_notes'     => $validated['discharge_notes'],
            'followup_date'       => $validated['followup_date'] ?? null,
            'discharged_at'       => now(),
            'final_diagnosis'     => $validated['discharge_notes'],
        ]);

        $patient->bed?->release();

        if ($validated['discharge_condition'] === 'deceased') {
            MortuaryRecord::create([
                'patient_id'     => $patient->id,
                'referred_by'    => Auth::id(),
                'cause_of_death' => $validated['cause_of_death'] ?? null,
                'notes'          => $validated['discharge_notes'],
                'status'         => 'pending',
            ]);

            return redirect()
                ->route('patients.discharge')
                ->with('success', "{$patient->name} has been discharged and the mortuary has been notified.");
        }

        return redirect()
            ->route('patients.discharge')
            ->with('success', "{$patient->name} has been discharged successfully.");
    }

    // ── Mortuary Transfer ─────────────────────────────────────────────

    public function transferToMortuary(Request $request, Patient $patient): RedirectResponse
    {
        if ($redirect = $this->denyIfNotAttendingDoctor($patient)) {
            return $redirect;
        }

        if ($patient->status === 'deceased') {
            return redirect()
                ->route('patients.index')
                ->with('error', 'This patient has already been transferred to the mortuary.');
        }

        $validated = $request->validate([
            'time_of_death'  => ['required', 'date'],
            'cause_of_death' => ['required', 'string', 'max:255'],
            'body_tag'       => ['required', 'string', 'max:100', 'unique:mortuary_records,body_tag'],
            'notes'          => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($patient, $validated) {
            $patient->update(['status' => 'deceased']);

            $patient->bed?->release();

            MortuaryRecord::create([
                'patient_id'     => $patient->id,
                'referred_by'    => Auth::id(),
                'time_of_death'  => $validated['time_of_death'],
                'cause_of_death' => $validated['cause_of_death'],
                'body_tag'       => $validated['body_tag'],
                'notes'          => $validated['notes'] ?? null,
                'status'         => 'pending',
            ]);

            $this->autoGenerateBill($patient);
        });

        return redirect()
            ->route('patients.index')
            ->with('success', "{$patient->name} has been transferred to the mortuary.");
    }

    // ── Clinical Notes ────────────────────────────────────────────────

    public function addNote(Request $request, Patient $patient)
    {
        if ($redirect = $this->denyIfNotAttendingDoctor($patient)) {
            return $redirect;
        }

        $validated = $request->validate([
            'content' => 'required|string',
            'type'    => 'nullable|in:progress,assessment,observation,procedure',
        ]);

        $patient->clinicalNotes()->create([
            'content' => $validated['content'],
            'type'    => $validated['type'] ?? 'progress',
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Clinical note added.');
    }

    // ── Prescriptions ─────────────────────────────────────────────────

    public function createPrescription(Patient $patient)
    {
        if ($redirect = $this->denyIfNotAttendingDoctor($patient)) {
            return $redirect;
        }

        $stockItems = StockItem::orderBy('name')->get();
        return view('patients.prescriptions_create', compact('patient', 'stockItems'));
    }

    public function addPrescription(Request $request, Patient $patient)
    {
        if ($redirect = $this->denyIfNotAttendingDoctor($patient)) {
            return $redirect;
        }

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

        $durationDays = null;
        if (!empty($validated['duration'])) {
            preg_match('/(\d+)/', $validated['duration'], $matches);
            $num = isset($matches[1]) ? (int) $matches[1] : 1;

            if (str_contains(strtolower($validated['duration']), 'week')) {
                $durationDays = $num * 7;
            } elseif (str_contains(strtolower($validated['duration']), 'month')) {
                $durationDays = $num * 30;
            } else {
                $durationDays = $num;
            }
        }

        $patient->prescriptions()->create([
            'medication_name' => $stockItem->name,
            'doctor_id'       => Auth::id(),
            'dosage'          => $validated['dosage'],
            'frequency'       => $validated['frequency'],
            'duration_days'   => $durationDays,
            'instructions'    => $validated['instructions'] ?? null,
        ]);

        if (!empty($validated['quantity'])) {
            $stockItem->decrement('quantity', $validated['quantity']);
        }

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Prescription issued successfully.');
    }

    // ── My Patients (doctor's filtered view) ───────────────────────────

    public function myPatients(Request $request)
    {
        $patients = Patient::query()
            ->assignedTo(Auth::id())
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest('admitted_at')
            ->paginate(20)
            ->withQueryString();

        return view('patients.my_patients', compact('patients'));
    }
}
