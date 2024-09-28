<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StarService extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'star',
        'comment',
        'status',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }
}
