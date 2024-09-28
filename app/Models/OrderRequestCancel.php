<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRequestCancel extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'title',
        'reason',
        'bank_code_id',
        'agencia',
        'agencia_dv_id',
        'conta_id',
        'conta_dv_id',
        'type',
        'document_number_id',
        'legal_name_id',
    ];
}
