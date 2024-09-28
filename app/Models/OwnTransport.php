<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnTransport extends Model
{
    use HasFactory;

    protected $casts = [
        'semana' => 'array'
    ];

    protected $fillable = [
        'seller_id',
        'estado',
        'cidade',
        'toda_cidade',
        'em_todas_cidades',
        'bairro',
        'valor_entrega',
        'tempo_entrega',
        'tempo',
        'semana',
        'descricao',
        'frete_gratis',
        'valor_minimo',
    ];
}
