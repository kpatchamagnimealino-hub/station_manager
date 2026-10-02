<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'stock_quantity',
        'minimum_stock',
        'price',
        'description',
    ];

    protected $casts = [
        'stock_quantity' => 'float',
        'minimum_stock' => 'float',
        'price' => 'float',
    ];

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->minimum_stock;
    }

    public function supplies(): HasMany
    {
        return $this->hasMany(Supply::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function stationStocks(): HasMany
    {
        return $this->hasMany(StationStock::class);
    }
}
