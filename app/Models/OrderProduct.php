<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $casts = [
        'attributes' => 'array',
    ];

    protected $fillable = [
        'order_number',
        'sequence',
        'seller_id',
        'product_id',
        'product_code',
        'product_name',
        'product_price',
        'quantity',
        'product_weight',
        'product_height',
        'product_width',
        'product_length',
        'product_sales_unit',
        'attributes',
        'discount',
        'note',
        'active',
    ];

    public function stars()
    {
        return $this->hasMany(StarProduct::class, 'product_id', 'product_id');
    }

    public function product()
    {
        return $this->hasOne(Produto::class, 'id', 'product_id');
    }

    public function seller()
    {
        return $this->hasOne(seller::class, 'id', 'seller_id');
    }

    public function viewProduct()
    {
        return $this->hasMany(viewProductsService::class, 'id_reference', 'product_id')->where('reference_type', 'product');
    }
}
