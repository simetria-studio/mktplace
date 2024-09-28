<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceReservation extends Model
{
    use HasFactory;

    protected $casts = [
        'attributes' => 'array'
    ];

    protected $fillable = [
        'order_number',
        'service_id',
        'seller_id',
        'user_id',
        'service_name',
        'service_price',
        'service_quantity',
        'attributes',
        'date_reservation_ini',
        'date_reservation_fim',
        'hour_reservation',
        'status',
        'active',
    ];

    public function stars()
    {
        return $this->hasMany(StarService::class, 'service_id', 'service_id');
    }

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }

    public function seller()
    {
        return $this->hasOne(seller::class, 'id', 'seller_id');
    }

    public function viewService()
    {
        return $this->hasMany(viewProductsService::class, 'id_reference', 'service_id')->where('reference_type', 'service');
    }

    public function customerAddress()
    {
        return $this->hasOne(CustomerAddress::class, 'user_id', 'user_id');
    }
}
