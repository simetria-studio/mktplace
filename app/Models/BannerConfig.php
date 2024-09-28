<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'local',
        'link',
        'new_tab',
        'file_name',
        'path_file',
        'url_file',
    ];
}
