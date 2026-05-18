<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ProgressUpdate extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'wip_id',
        'user_id',
        'content',
        'percentage_completed',
    ];

    public function wip()
    {
        return $this->belongsTo(Wip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
