<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StationStock extends Model
{
    protected $fillable = [
        'station_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'float',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->product->minimum_stock;
    }
}
