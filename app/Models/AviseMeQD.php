<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AviseMeQD extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'name',
        'email',
        'status',
    ];
}
