<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MortuaryRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'referred_by', 'received_by',
        'cause_of_death', 'notes', 'status',
        'received_at', 'released_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function patient()    { return $this->belongsTo(Patient::class); }
    public function referredBy() { return $this->belongsTo(User::class, 'referred_by'); }
    public function receivedBy() { return $this->belongsTo(User::class, 'received_by'); }

    public function scopePending($q)  { return $q->where('status', 'pending'); }
    public function scopeReceived($q) { return $q->where('status', 'received'); }
}
