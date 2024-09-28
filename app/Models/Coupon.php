<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee',
        'code_coupon',
        'coupon_valid',
        'discount_config',
        'value_discount',
        'value_min',
        'value_max',
        'status',
    ];

    public function sellers()
    {
        return $this->hasMany(CouponSeller::class);
    }
}
