<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponSeller extends Model
{
    use HasFactory;

    protected $casts = [
        'product_id' => 'array',
        'service_id' => 'array',
    ];

    protected $fillable = [
        'coupon_id',
        'seller_id',
        'check_loja',
        'product_id',
        'service_id',
    ];
}
