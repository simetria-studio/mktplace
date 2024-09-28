<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    use HasFactory;

    protected $casts = [
        'keywords' => 'array'
    ];

    protected $fillable = [
        'nome',
        'slug',
        'descricao_curta',
        'preco',
        'weight',
        'perecivel',
        'height',
        'width',
        'length',
        'descricao_completa',
        'seller_id',
        'marca_id',
        'status',
        'ativo',
        'stock_controller',
        'stock',
        'title',
        'link',
        'keywords',
        'description',
        'banner_path',
    ];

    protected $guarded = ['id'];

    public function seller()
    {
        return $this->hasOne(Seller::class, 'id' , 'seller_id');
    }

    public function images()
    {
        return $this->hasMany(related: ImagensProduto::class);
    }

    public function atributos()
    {
        return $this->belongsToMany(Attribute::class)->using(AttributeProduto::class)->withTimestamps();
    }

    public function variations()
    {
        return $this->hasMany(VariationsProduto::class);
    }

    public function getValorVendedor()
    {
        $valorProduto = (float)$this->preco;
        return (float)($valorProduto - $this->getValorBiguacu());
    }

    public function getValorBiguacu()
    {
        $valorProduto = (float)$this->preco;
        // aqui vira a regra de porcentagem que a biguacu irá receber...
        return round(($valorProduto * 0.17), 2);
    }

    public function categories()
    {
        return $this->hasMany(ProductCategory::class, 'product_id', 'id');
    }

    public function favorite()
    {
        return $this->hasMany(Favorite::class, 'product_id', 'id');
    }

    public function fatoresProduto()
    {
        return $this->hasMany(ProdutoFactor::class, 'product_id', 'id');
    }

    public function attrs()
    {
        return $this->hasMany(AttributeProduct::class, 'product_id', 'id');
    }
    public function attrAttrs()
    {
        return $this->hasMany(AttributeProduct::class, 'product_id', 'id');
    }

    public function stars()
    {
        return $this->hasMany(StarProduct::class, 'product_id', 'id');
    }

    public function planPurchases()
    {
        return $this->hasMany(PlanPurchase::class, 'reference_id', 'id')->where('reference_type','product');
    }

    public function progressiveDiscount()
    {
        return $this->hasMany(ProgressiveDiscount::class, 'reference_id', 'id')->where('reference_type','product');
    }

    public function affiliatedProduct()
    {
        return $this->hasOne(AffiliateItem::class, 'reference_id', 'id')->where('reference_type',0);
    }
}
