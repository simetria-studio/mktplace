<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'api_name',
        'token',
        'refresh_token',
        'expires_in',
        'other_information',
    ];

    protected $casts = [
        'other_information' => 'array',
    ];
}
