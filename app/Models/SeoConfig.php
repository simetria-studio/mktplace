<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoConfig extends Model
{
    use HasFactory;

    protected $casts = [
        'keywords' => 'array'
    ];

    protected $fillable = [
        'page',
        'title',
        'link',
        'keywords',
        'description',
        'banner_path',
    ];
}
