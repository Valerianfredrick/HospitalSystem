<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'specialty'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Patients this user is the attending doctor for. Used on the admin
     * dashboard (User::withCount('patients')) to show each user's
     * current patient load, and to power a doctor's "My Patients" list.
     */
    public function patients()
    {
        return $this->hasMany(Patient::class, 'doctor_id');
    }

    // ── Scopes used by patient triage/routing ───────────────────────

    public function scopeDoctors($query)
    {
        return $query->where('role', 'doctor');
    }

    public function scopeWithSpecialty($query, string $specialty)
    {
        return $query->where('specialty', $specialty);
    }
}
