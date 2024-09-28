<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'coupon' => 'array'
    ];

    protected $fillable = [
        'payment_id',
        'order_number',
        'parent_id',
        'seller_id',
        'user_id',
        'user_name',
        'user_email',
        'user_cnpj_cpf',
        'birth_date',
        'total_value',
        'cost_freight',
        'product_value',
        'pay',
        'discount',
        'coupon_value',
        'coupon',
        'payment_method',
        'note',
        'path_fiscal',
        'url_fiscal',
        'status_v',
        'active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function orderParent()
    {
        return $this->hasMany(Order::class, 'parent_id', 'id');
    }

    public function orderParentInverse()
    {
        return $this->hasOne(Order::class, 'id', 'parent_id');
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class, 'order_number', 'order_number');
    }

    public function shippingCustomer()
    {
        return $this->belongsTo(ShippingCustomer::class, 'order_number', 'order_number');
    }

    public function customerAddress()
    {
        return $this->belongsTo(CustomerAddress::class, 'user_id', 'user_id');
    }
}
