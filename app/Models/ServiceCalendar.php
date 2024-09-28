<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCalendar extends Model
{
    use HasFactory;

    protected $casts = [
        'semana' => 'array'
    ];

    protected $fillable = [
        'reference_id',
        'reference_type',
        'data_inicial',
        'data_fim',
        'select_termino',
        'antecedencia',
        'number_select',
        'select_control',
        'ocorrencia',
        'semana',
    ];
}
