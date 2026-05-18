<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'category', 'manufacturer', 'quantity',
        'unit', 'reorder_level', 'expiry_date', 'unit_price', 'location',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'reorder_level');
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays($days))
            ->whereDate('expiry_date', '>=', now());
    }

    public function getIsLowAttribute(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
