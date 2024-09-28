<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceVariantion extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'preco',
        'vaga',
    ];

    public function variations()
    {
        return $this->hasMany(ServiceVariantionValues::class);
    }

    public function calendars()
    {
        return $this->hasMany(ServiceCalendar::class, 'reference_id')->where('reference_type', 'service_var');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function progressiveDiscount()
    {
        return $this->hasMany(ProgressiveDiscount::class, 'reference_id', 'id')->where('reference_type','service_attr');
    }
}
