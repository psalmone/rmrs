<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PalayIntake extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'farmer_id',
        'farmer_name',
        'variety_type',
        'bag_count',
        'gross_weight_kg',
        'tare_weight_kg',
        'moisture_content_pct',
        'deduction_kg',
        'net_weight_kg',
        'price_per_kg',
        'total_amount',
        'payment_status',
        'received_by_user_id',
        'intake_date',
        'notes',
    ];

    protected $casts = [
        'intake_date' => 'datetime',
        'gross_weight_kg' => 'decimal:2',
        'tare_weight_kg' => 'decimal:2',
        'moisture_content_pct' => 'decimal:2',
        'deduction_kg' => 'decimal:2',
        'net_weight_kg' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    public function millingBatches()
    {
        return $this->hasMany(MillingBatch::class);
    }
}
