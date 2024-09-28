<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'name',
        'reference_type',
        'price_type',
        'price',
        'status',
    ];

    public function product()
    {
        return $this->hasOne(Produto::class, 'id', 'reference_id');
    }

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'reference_id');
    }

    public function affiliatePs()
    {
        return $this->hasMany(AffiliatePs::class, 'affiliate_item');
    }
}