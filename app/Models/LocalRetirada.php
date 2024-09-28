<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalRetirada extends Model
{
    use HasFactory;

    protected $fillable = [
       'seller_id',
       'localidade_id',
       'description',
       'products_id',
       'all_products',
    ];

    protected $casts = [
        'products_id' => 'array'
    ];

    public function localidade()
    {
        return $this->belongsTo(LocalidadeRetirada::class, 'localidade_id', 'id');
    }

    public function getProductsAttribute()
    {
        $relatedIds = $this->products_id;
        if (!is_array($relatedIds)) {
            $relatedIds = json_decode($relatedIds, true);
        }

        return Produto::whereIn('id', $relatedIds)->get();
    }
}
