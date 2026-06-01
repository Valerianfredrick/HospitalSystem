<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'generic_name',
        'barcode',
        'sku',
        'category',
        'unit',
        'quantity',
        'reorder_level',
        'unit_price',
        'supplier_name',
        'supplier_contact',
        'expiry_date',
        'manufacture_date',
        'notes',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'expiry_date'      => 'date',
        'manufacture_date' => 'date',
        'unit_price'       => 'decimal:2',
        'is_active'        => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Accessors / helpers ───────────────────────────────────────────────

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity > 0 && $this->quantity <= $this->reorder_level;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->quantity === 0;
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_out_of_stock) return 'Out of Stock';
        if ($this->is_low_stock)    return 'Low Stock';
        return 'In Stock';
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->is_out_of_stock) return 'red';
        if ($this->is_low_stock)    return 'amber';
        return 'green';
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'reorder_level')
            ->where('quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', 0);
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', now())
            ->whereDate('expiry_date', '<=', now()->addDays($days));
    }
}
