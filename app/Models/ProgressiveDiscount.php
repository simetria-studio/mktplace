<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressiveDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'reference_type',
        'discount_quantity',
        'discount_value',
    ];
}
