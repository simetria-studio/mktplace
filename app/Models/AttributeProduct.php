<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'attribute_id',
    ];

    public function attribute()
    {
        return $this->hasOne(Attribute::class, 'id', 'attribute_id');
    }
}
