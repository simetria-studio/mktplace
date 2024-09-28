<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank',
        'branch_number',
        'branch_check_digit',
        'account_number',
        'account_check_digit',
        'type',
        'holder_document',
        'holder_name',
        'holder_type',
        'wallet_id',
        'status',
    ];

    public function users()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}