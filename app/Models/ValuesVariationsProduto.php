<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValuesVariationsProduto extends Model
{
    use HasFactory;

    protected $fillable = [
        'variations_produto_id',
        'attribute_id',
        'attribute_pai_id',
    ];

    public function variationProduto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(VariationsProduto::class);
    }

    public function atributos(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}
