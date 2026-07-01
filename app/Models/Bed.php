<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = ['ward_id', 'bed_number', 'status', 'patient_id'];

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && is_null($this->patient_id);
    }

    /**
     * Assign a patient to this bed: marks the bed occupied and
     * links the patient's bed_id back to this bed.
     */
    public function assignPatient(Patient $patient): void
    {
        $this->update([
            'status'     => 'occupied',
            'patient_id' => $patient->id,
        ]);

        $patient->update(['bed_id' => $this->id]);
    }

    /**
     * Free this bed: clears the patient link on both sides and
     * marks the bed available again.
     */
    public function release(): void
    {
        if ($this->patient_id) {
            Patient::where('id', $this->patient_id)
                ->where('bed_id', $this->id)
                ->update(['bed_id' => null]);
        }

        $this->update([
            'status'     => 'available',
            'patient_id' => null,
        ]);
    }
}
