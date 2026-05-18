<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['stock_item_id', 'user_id', 'prescription_id', 'type', 'quantity', 'notes'];

    public function stockItem()    { return $this->belongsTo(StockItem::class); }
    public function user()         { return $this->belongsTo(User::class); }
    public function prescription() { return $this->belongsTo(Prescription::class); }
}
