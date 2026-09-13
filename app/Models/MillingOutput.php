<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MillingOutput extends Model
{
    use HasFactory;

    protected $fillable = [
        'milling_batch_id',
        'item_id',
        'output_type',
        'quantity_units',
        'weight_kg',
    ];

    protected $casts = [
        'quantity_units' => 'decimal:2',
        'weight_kg' => 'decimal:2',
    ];

    public function batch()
    {
        return $this->belongsTo(MillingBatch::class, 'milling_batch_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
