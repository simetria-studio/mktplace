<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSignedPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'signed_plan_id',
        'fatura_id',
        'cobranca_id',
        'status',
    ];
}
