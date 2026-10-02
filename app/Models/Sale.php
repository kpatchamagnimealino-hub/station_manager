<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'station_id',
        'product_id',
        'quantity',
        'unit_price',
        'sold_at',
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'sold_at' => 'datetime',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
