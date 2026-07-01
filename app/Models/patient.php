<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'date_of_birth', 'gender', 'phone', 'address',
        'emergency_contact_name', 'emergency_contact_phone',
        'doctor_id', 'bed_id', 'status',
        'diagnosis', 'admitted_at',
        'blood_pressure', 'pulse', 'temperature', 'weight',
        'discharged_at', 'final_diagnosis', 'discharge_notes',
        'followup_date', 'discharge_condition',
    ];

    protected $casts = [
        'admitted_at'   => 'datetime',
        'discharged_at' => 'datetime',
        'followup_date' => 'date',
        'date_of_birth' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function clinicalNotes()
    {
        return $this->hasMany(PatientNote::class)->orderByDesc('created_at');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class)->orderByDesc('created_at');
    }

    public function labRequests()
    {
        return $this->hasMany(LabRequest::class)->orderByDesc('created_at');
    }

    /**
     * The patient's single active/primary bill (hasOne).
     * Use this when you expect one bill per patient.
     */
    public function bill()
    {
        return $this->hasOne(Bill::class)->latestOfMany();
    }

    /**
     * All bills for this patient (hasMany).
     * Use $patient->bills when iterating or eager-loading multiple bills.
     */
    public function bills()
    {
        return $this->hasMany(Bill::class)->orderByDesc('created_at');
    }

    public function mortuaryRecord()
    {
        return $this->hasOne(MortuaryRecord::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────

    public function scopeAdmitted($query)
    {
        return $query->whereNotIn('status', ['discharged']);
    }

    public function scopeCritical($query)
    {
        return $query->where('status', 'critical');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('diagnosis', 'like', "%{$term}%")
                ->orWhere('id', 'like', "%{$term}%");
        });
    }

    /**
     * Patients assigned to a specific doctor (by doctor_id). Used to
     * power a doctor's "My Patients" filtered list.
     */
    public function scopeAssignedTo($query, int $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    // ── Accessors ──────────────────────────────────────────────────────

    public function getAgeAttribute()
    {
        return $this->date_of_birth?->age;
    }

    public function getDaysAdmittedAttribute(): int
    {
        if (!$this->admitted_at) return 0;
        $end = $this->discharged_at ?? now();
        return (int) $this->admitted_at->diffInDays($end);
    }

    /**
     * Replaces the old plain-string `ward` column. Resolves through the
     * patient's assigned bed -> ward relationship. Returns null if the
     * patient has no bed assigned (e.g. not yet admitted, or discharged
     * and their bed was released).
     */
    public function getWardNameAttribute(): ?string
    {
        return $this->bed?->ward?->name;
    }

    /**
     * Replaces the old plain-string `bed_number` column.
     */
    public function getBedNumberAttribute(): ?string
    {
        return $this->bed?->bed_number;
    }
}
