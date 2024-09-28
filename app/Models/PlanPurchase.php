<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'reference_type',
        'plan_title',
        'select_interval',
        'duration_plan',
        'plan_value',
        'select_entrega',
        'descption_plan',
        'peso',
        'dimensoes_C',
        'dimensoes_L',
        'dimensoes_A',
    ];
}
