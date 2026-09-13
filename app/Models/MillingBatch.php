<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MillingBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_number',
        'operator_user_id',
        'palay_intake_id',
        'palay_variety',
        'input_sacks',
        'input_weight_kg',
        'started_at',
        'completed_at',
        'status',
        'total_milled_output_kg',
        'recovery_rate_pct',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'input_weight_kg' => 'decimal:2',
        'total_milled_output_kg' => 'decimal:2',
        'recovery_rate_pct' => 'decimal:2',
    ];

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_user_id');
    }

    public function palayIntake()
    {
        return $this->belongsTo(PalayIntake::class);
    }

    public function outputs()
    {
        return $this->hasMany(MillingOutput::class);
    }
}
