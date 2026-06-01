<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'created_by', 'processed_by',
        'bed_days', 'bed_rate_per_day', 'bed_total',
        'lab_total', 'drugs_total', 'extra_charges',
        'grand_total', 'amount_paid', 'balance',
        'status', 'payment_method', 'notes', 'paid_at',
    ];

    protected $casts = [
        'extra_charges' => 'array',
        'paid_at'       => 'datetime',
        'bed_total'     => 'decimal:2',
        'lab_total'     => 'decimal:2',
        'drugs_total'   => 'decimal:2',
        'grand_total'   => 'decimal:2',
        'amount_paid'   => 'decimal:2',
        'balance'       => 'decimal:2',
    ];

    public function patient()    { return $this->belongsTo(Patient::class); }
    public function createdBy()  { return $this->belongsTo(User::class, 'created_by'); }
    public function processedBy(){ return $this->belongsTo(User::class, 'processed_by'); }

    public function scopeUnpaid($q)  { return $q->where('status', 'unpaid'); }
    public function scopePaid($q)    { return $q->where('status', 'paid'); }
}
