<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'date_of_birth', 'gender', 'phone', 'address',
        'emergency_contact_name', 'emergency_contact_phone',
        'doctor_id', 'ward', 'bed_number', 'status',
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

    public function bill()
    {
        return $this->hasOne(Bill::class);
    }

    public function mortuaryRecord()
    {
        return $this->hasOne(MortuaryRecord::class);
    }
}

