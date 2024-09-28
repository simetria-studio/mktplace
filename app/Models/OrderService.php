<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderService extends Model
{
    use HasFactory;

    protected $casts = [
        'coupon' => 'array',
    ];

    protected $fillable = [
        'order_number',
        'seller_id',
        'user_id',
        'user_name',
        'user_email',
        'user_cnpj_cpf',
        'birth_date',
        'total_value',
        'service_value',
        'pay',
        'discount',
        'coupon_value',
        'coupon',
        'payment_method',
        'payment_id',
        'note',
        'path_fiscal',
        'url_fiscal',
        'active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->hasOne(Seller::class, 'id', 'seller_id');
    }

    public function serviceReservation()
    {
        return $this->hasOne(ServiceReservation::class, 'order_number', 'order_number');
    }
}
