<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabelaGeral extends Model
{
    use HasFactory;

    protected $casts = [
        'array_text' => 'array'
    ];

    protected $fillable = [
        'tabela',
        'coluna',
        'valor',
        'array_text',
        'long_text',
    ];
}
