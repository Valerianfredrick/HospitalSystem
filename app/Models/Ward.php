<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    public function totalBeds(): int
    {
        return $this->beds()->count();
    }

    public function occupiedBeds(): int
    {
        return $this->beds()->where('status', 'occupied')->count();
    }

    public function availableBeds(): int
    {
        return $this->beds()->where('status', 'available')->count();
    }

    public function maintenanceBeds(): int
    {
        return $this->beds()->where('status', 'maintenance')->count();
    }
}
