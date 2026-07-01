<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Patient;
use App\Models\Ward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WardController extends Controller
{
    /**
     * Ward Overview — shows every ward with its beds and occupancy status.
     */
    public function index()
    {
        $wards = Ward::with(['beds.patient'])
            ->orderBy('name')
            ->get();

        $summary = [
            'total_wards'     => $wards->count(),
            'total_beds'      => $wards->sum(fn($w) => $w->beds->count()),
            'occupied_beds'   => $wards->sum(fn($w) => $w->beds->where('status', 'occupied')->count()),
            'available_beds'  => $wards->sum(fn($w) => $w->beds->where('status', 'available')->count()),
            'maintenance_beds'=> $wards->sum(fn($w) => $w->beds->where('status', 'maintenance')->count()),
        ];

        return view('wards.index', compact('wards', 'summary'));
    }

    /**
     * Single ward detail (optional drill-down from the overview grid).
     */
    public function show(Ward $ward)
    {
        $ward->load(['beds.patient']);

        return view('wards.show', compact('ward'));
    }

    /**
     * Assign a patient to a specific bed.
     * Used by the patient admission/edit form's bed-picker dropdown.
     */
    public function assignBed(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'bed_id' => 'required|exists:beds,id',
        ]);

        $bed = Bed::findOrFail($validated['bed_id']);

        if (!$bed->isAvailable()) {
            return back()->with('error', 'That bed is no longer available. Please choose another.');
        }

        // Free the patient's previous bed, if any, before assigning the new one.
        if ($patient->bed_id) {
            $patient->bed?->release();
        }

        $bed->assignPatient($patient);

        return back()->with('success', "{$patient->name} assigned to Bed {$bed->bed_number} ({$bed->ward->name}).");
    }

    /**
     * Mark a bed as under maintenance (taking it out of the available pool).
     */
    public function setMaintenance(Bed $bed): RedirectResponse
    {
        if ($bed->patient_id) {
            return back()->with('error', 'Cannot set an occupied bed to maintenance. Discharge or move the patient first.');
        }

        $bed->update(['status' => 'maintenance']);

        return back()->with('success', "Bed {$bed->bed_number} marked under maintenance.");
    }

    /**
     * Return a maintenance bed to the available pool.
     */
    public function clearMaintenance(Bed $bed): RedirectResponse
    {
        $bed->update(['status' => 'available']);

        return back()->with('success', "Bed {$bed->bed_number} is now available.");
    }
}
