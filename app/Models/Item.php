<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'variety',
        'unit',
        'current_stock',
        'reorder_level',
        'unit_cost',
        'unit_price',
        'is_active',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function millingOutputs()
    {
        return $this->hasMany(MillingOutput::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
