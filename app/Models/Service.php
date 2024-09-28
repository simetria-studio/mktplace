<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $casts = [
        'keywords' => 'array'
    ];

    protected $fillable = [
        'seller_id',
        'service_title',
        'short_description',
        'service_slug',
        'preco',
        'vaga',
        'check_variation',
        'vaga_controller',
        'address_controller',
        'hospedagem_controller',
        'selecao_hospedagem',
        'qty_max_hospedagem',
        'postal_code',
        'address',
        'number',
        'complement',
        'address2',
        'state',
        'city',
        'phone',
        'latitude',
        'longitude',
        'full_description',
        'description',
        'title',
        'link',
        'keywords',
        'banner_path',
        'whatsapp',
        'text_contact',
        'status',
    ];

    public function seller()
    {
        return $this->hasOne(Seller::class, 'id' ,'seller_id');
    }
    public function store()
    {
        return $this->hasOne(Store::class, 'user_id' ,'seller_id');
    }

    public function categories()
    {
        return $this->hasMany(ServiceCategory::class);
    }

    public function images()
    {
        return $this->hasMany(ImagensService::class);
    }

    public function attrAttrs()
    {
        return $this->hasMany(AttributeService::class);
    }

    public function variations()
    {
        return $this->hasMany(ServiceVariantion::class);
    }

    public function calendars()
    {
        return $this->hasMany(ServiceCalendar::class, 'reference_id')->where('reference_type', 'service');
    }

    public function fatoresServico()
    {
        return $this->hasMany(ServiceFactor::class);
    }

    public function favorite()
    {
        return $this->hasMany(ServiceFavorite::class);
    }

    public function stars()
    {
        return $this->hasMany(StarService::class);
    }

    public function serviceReservation()
    {
        return $this->hasMany(ServiceReservation::class, 'service_id');
    }

    public function progressiveDiscount()
    {
        return $this->hasMany(ProgressiveDiscount::class, 'reference_id', 'id')->where('reference_type','service');
    }

    public function affiliatedService()
    {
        return $this->hasOne(AffiliateItem::class, 'reference_id', 'id')->where('reference_type', 1);
    }
}
