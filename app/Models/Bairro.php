<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bairro extends Model
{
    use HasFactory;

    protected $table = 'm2_localidade_bairro';

    protected $fillable = [
        'localidade_estado_id',
        'localidade_municipio_id',
        'titulo',
    ];

    public $timestamps = false;

    public function estado()
    {
        return $this->hasOne(Estado::class, 'id', 'localidade_estado_id');
    }

    public function cidade()
    {
        return $this->hasOne(Cidade::class, 'id', 'localidade_municipio_id');
    }
}
