<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventHomeRural extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
        'new_tab',
        'file_name',
        'path_file',
        'url_file',
        'posicao',
        'status',
        'descricao_curta',
    ];
}
