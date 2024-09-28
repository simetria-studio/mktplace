<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceVariantionValues extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_variantion_id',
        'attribute_id',
        'attribute_pai_id',
    ];
}
