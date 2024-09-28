<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Seller extends Authenticatable
{
    use HasFactory;
    use Notifiable;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'document',
        'document_type',
        'inscricao_estadual',
        'birth_date',
        'code_delete',
        'phone',
        'responsavel_id',
        'wallet_id',
        'status',
    ];

     /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function store()
    {
        return $this->hasOne(Store::class, 'user_id');
    }

    public function transport()
    {
        return $this->hasMany(OwnTransport::class, 'seller_id' , 'id');
    }

    public function attributesVendedor(){
        return
            $this->hasMany(
            Attribute::class, 'seller_id'
        );
    }
}
