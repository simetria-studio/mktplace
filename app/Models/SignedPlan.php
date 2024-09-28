<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignedPlan extends Model
{
    use HasFactory;

    protected $casts = [
        'cart' => 'array',
        'product' => 'array',
        'shipping' => 'array',
    ];

    protected $fillable = [
        'pagarme_id',
        'user_id',
        'seller_id',
        'plan_id',
        'product_id',
        'product_name',
        'plan_title',
        'select_interval',
        'duration_plan',
        'plan_value',
        'select_entrega',
        'cart',
        'product',
        'shipping',
        'finish',
        'observation',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller(){
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function produto()
    {
        return $this->hasOne(Produto::class, 'id', 'product_id');
    }

    public function sub_signed_plan(){
        return $this->hasMany(SubSignedPlan::class);
    }
}