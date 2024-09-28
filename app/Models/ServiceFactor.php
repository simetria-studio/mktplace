<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceFactor extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'field_name',
        'field_value',
        'status',
    ];
}
