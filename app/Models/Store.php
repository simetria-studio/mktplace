<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Store extends Model
{
    use HasFactory;

    use HasSlug;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('store_name')
            ->saveSlugsTo('store_slug');
    }

    protected $casts = [
        'keywords' => 'array'
    ];

    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'post_code',
        'state',
        'city',
        'address2',
        'address',
        'number',
        'complement',
        'phone1',
        'phone2',
        'logo_path',
        'banner_path',
        'title',
        'link',
        'keywords',
        'description',
        'banner_path_two',
        'retirada',
        'ob_retirada',
        'lat',
        'lng',
    ];
}
