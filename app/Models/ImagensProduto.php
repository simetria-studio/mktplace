<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagensProduto extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'legenda',
        'texto_alternativo',
        'pasta',
        'principal',
        'caminho',
        'position',
        'produto_id',
    ];
}
