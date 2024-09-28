<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSaleAffiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id',
        'reference_id',
        'type_reference',
        'order_number',
        'qty',
        'value',
    ];

    public function orderP()
    {
        return $this->hasOne(Order::class, 'order_number', 'order_number');
    }

    public function orderS()
    {
        return $this->hasOne(OrderService::class, 'order_number', 'order_number');
    }

    public function product()
    {
        return $this->hasOne(Produto::class, 'id', 'reference_id');
    }

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'reference_id');
    }
}
