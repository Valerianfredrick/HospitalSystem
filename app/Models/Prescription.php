<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'medication_name', 'dosage',
        'frequency', 'duration_days', 'instructions',
        'is_dispensed', 'dispensed_at', 'dispensed_by',
    ];

    protected $casts = [
        'is_dispensed' => 'boolean',
        'dispensed_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // ✅ Alias so blade $rx->prescribedBy->name works
    public function prescribedBy()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function dispensedBy()
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }
}
