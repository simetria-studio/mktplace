<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VariationsProduto extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'produto_id',
        'preco',
        'peso',
        'dimensoes_A',
        'dimensoes_C',
        'dimensoes_L',
    ];

    /**
     *
     * Os atributos do produto
     *
     * @return BelongsToMany
     */
    public function atributos(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'values_variations_produtos');
    }

    public function variations()
    {
        return $this->hasMany(ValuesVariationsProduto::class, 'variations_produto_id', 'id');
    }

    /**
     *
     * A variação precisa ser de um produto
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function produto(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    public function progressiveDiscount()
    {
        return $this->hasMany(ProgressiveDiscount::class, 'reference_id', 'id')->where('reference_type','product_attr');
    }
}
