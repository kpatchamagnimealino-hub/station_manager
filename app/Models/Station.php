<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    protected $fillable = [
        'name',
        'location',
        'status',
    ];

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
