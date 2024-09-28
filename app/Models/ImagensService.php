<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagensService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'legenda',
        'texto_alternativo',
        'caminho',
        'pasta',
        'principal',
        'position',
    ];
}
