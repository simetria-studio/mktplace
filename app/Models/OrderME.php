<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderME extends Model
{
    use HasFactory;

    protected $casts = [
        'package' => 'array',
    ];

    protected $fillable = [
        'order_number',
        'seller_id',
        'company_id',
        'service_id',
        'transport',
        'agency_id',
        'order_id',
        'code',
        'price',
        'package',
        'height',
        'width',
        'length',
        'weight',
    ];
}
