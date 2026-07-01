<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Patient;
use App\Models\User;
use App\Models\Ward;
use App\Services\TriageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceptionistController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────────

    public function dashboard()
    {
        $recentRegistrations = Patient::latest('admitted_at')
            ->take(8)
            ->get();

        $stats = [
            'registered_today' => Patient::whereDate('admitted_at', today())->count(),
            'total_patients'   => Patient::count(),
            'unassigned'       => Patient::admitted()->whereNull('doctor_id')->count(),
        ];

        return view('receptionist.dashboard', compact('recentRegistrations', 'stats'));
    }

    // ── Registration form ─────────────────────────────────────────────

    public function create()
    {
        $wards = Ward::with('beds')->orderBy('name')->get();

        return view('receptionist.patients_create', compact('wards'));
    }

    // ── Store + triage ────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
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
            'bed_id'                  => 'nullable|exists:beds,id',

            // Receptionist can leave blank to auto-detect from diagnosis,
            // or pick one explicitly to override the guess.
            'specialty'               => 'nullable|string|max:50',
        ]);

        $bedId = $validated['bed_id'] ?? null;
        unset($validated['bed_id']);

        $validated['admitted_at'] = now();
        $validated['status']      = 'admitted';

        // ── Triage: figure out which specialty this patient needs ───
        $triage = new TriageService();

        $specialty = $validated['specialty'] ?? null;
        if (empty($specialty)) {
            $specialty = $triage->detectSpecialty($validated['diagnosis']);
        }
        unset($validated['specialty']); // not a Patient column — used only to pick a doctor

        // ── Find a doctor in that specialty, falling back to general ─
        $doctor = User::doctors()->withSpecialty($specialty)->inRandomOrder()->first();

        if (! $doctor && $specialty !== 'general') {
            $doctor = User::doctors()->withSpecialty('general')->inRandomOrder()->first();
        }

        $validated['doctor_id'] = $doctor?->id;

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

        $specialtyLabel = TriageService::labels()[$specialty] ?? $specialty;

        $message = "Patient {$patient->name} registered successfully.";
        $message .= $doctor
            ? " Routed to Dr. {$doctor->name} ({$specialtyLabel})."
            : " No doctor currently available for {$specialtyLabel} — patient is unassigned until one is added.";

        return redirect()
            ->route('receptionist.dashboard')
            ->with('success', $message);
    }

    // ── Read-only patient views ───────────────────────────────────────

    public function index(Request $request)
    {
        $patients = Patient::query()
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('receptionist.patients_index', compact('patients'));
    }

    public function admission(Request $request)
    {
        $patients = Patient::admitted()
            ->when($request->search, fn($q) => $q->search($request->search))
            ->latest('admitted_at')
            ->paginate(12)
            ->withQueryString();

        return view('receptionist.patients_admission', compact('patients'));
    }
}
