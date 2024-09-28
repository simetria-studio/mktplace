<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliatePs extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'affiliate_id',
        'affiliate_item',
        'url',
        'codigo',
        'status',
    ];

    public function user()
    {
        return $this->hasOne(AffiliateInfo::class, 'id', 'affiliate_id');
    }

    public function item()
    {
        return $this->hasOne(AffiliateItem::class, 'id', 'affiliate_item');
    }
}