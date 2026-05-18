<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaLibrary extends Model
{
    protected $table = 'media_library';

    protected $fillable = [
        'filename',
        'path',
        'type',
        'alt_text',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];
}
