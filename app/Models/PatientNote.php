<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientNote extends Model
{
    protected $fillable = ['patient_id', 'author_id', 'title', 'content'];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function author()  { return $this->belongsTo(User::class, 'author_id'); }
}
