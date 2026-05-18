<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeeklyReview extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'company_id',
        'week_number',
        'year',
        'summary',
        'highlights',
        'concerns',
        'submitted_by_id',
    ];

    protected $casts = [
        'highlights' => 'array',
        'concerns' => 'array',
    ];

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by_id');
    }
}
