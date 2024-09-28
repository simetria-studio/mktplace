<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewProductsService extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'email',
        'seller_id',
        'id_reference',
        'reference_type',
    ];
}
