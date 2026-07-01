<?php

use App\Models\Patient;
use App\Models\Ward;
use App\Models\Bed;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Default number of beds created for each auto-generated ward.
     * Existing patients will be placed into these beds; any overflow
     * (more currently-admitted patients in a ward than default beds)
     * gets extra beds created automatically so nobody is left unbedded.
     */
    private int $defaultBedsPerWard = 10;

    public function up(): void
    {
        // 1. Collect distinct, non-empty ward names currently in use.
        $wardNames = Patient::query()
            ->whereNotNull('ward')
            ->where('ward', '!=', '')
            ->distinct()
            ->pluck('ward');

        // Always ensure at least one fallback ward exists for patients
        // that had no ward set at all, or whose name is blank.
        $wardNames = $wardNames->push('Unassigned')->unique();

        foreach ($wardNames as $wardName) {
            $ward = Ward::firstOrCreate(['name' => $wardName]);

            // Pre-create the default bed allotment for this ward.
            for ($i = 1; $i <= $this->defaultBedsPerWard; $i++) {
                Bed::firstOrCreate([
                    'ward_id'    => $ward->id,
                    'bed_number' => (string) $i,
                ]);
            }
        }

        // 2. Link currently-admitted patients to a bed in their ward.
        //    Discharged/deceased patients are NOT force-assigned a bed —
        //    only patients whose status implies they're physically occupying one.
        $occupiableStatuses = ['admitted', 'stable', 'critical', 'recovering', 'observation'];

        Patient::query()
            ->whereIn('status', $occupiableStatuses)
            ->orderBy('id')
            ->chunkById(50, function ($patients) {
                foreach ($patients as $patient) {
                    $wardName = $patient->ward ?: 'Unassigned';
                    $ward = Ward::where('name', $wardName)->first();

                    if (!$ward) {
                        continue; // shouldn't happen given step 1, but stay safe
                    }

                    // Find a free bed in this ward; if none left, create an overflow bed
                    // so we never silently lose a patient's bed assignment.
                    $bed = Bed::where('ward_id', $ward->id)
                        ->where('status', 'available')
                        ->whereNull('patient_id')
                        ->orderBy('bed_number')
                        ->first();

                    if (!$bed) {
                        $nextNumber = Bed::where('ward_id', $ward->id)->count() + 1;
                        $bed = Bed::create([
                            'ward_id'    => $ward->id,
                            'bed_number' => (string) $nextNumber,
                        ]);
                    }

                    $bed->update([
                        'status'     => 'occupied',
                        'patient_id' => $patient->id,
                    ]);

                    DB::table('patients')
                        ->where('id', $patient->id)
                        ->update(['bed_id' => $bed->id]);
                }
            });
    }

    public function down(): void
    {
        // Data backfill — nothing structural to reverse here.
        // (Reversing bed/ward creation is handled by the table migrations' down().)
    }
};
