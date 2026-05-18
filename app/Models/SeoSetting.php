<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    protected $fillable = [
        'page_name',
        'title',
        'description',
        'keywords',
        'og_data',
    ];

    protected $casts = [
        'og_data' => 'array',
    ];
}
