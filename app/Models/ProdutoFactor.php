<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoFactor extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'field_name',
        'field_value',
        'status',
    ];
}
