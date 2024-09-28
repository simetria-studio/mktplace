<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasFactory;

    use HasSlug;

    protected $casts = [
        'keywords' => 'array'
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'type',
        'icon',
        'position',
        'title',
        'link',
        'keywords',
        'description',
        'banner_path',
    ];

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // public function promotionC()
    // {
    //     return $this->hasMany(Promotion::class, 'identifier');
    // }

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class, ProductCategory::class, 'category_id', 'product_id')->wherePivot('products.status', '1');
    // }
}
