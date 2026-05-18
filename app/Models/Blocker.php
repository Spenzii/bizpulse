<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Blocker extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'company_id',
        'wip_id',
        'raised_by_id',
        'description',
        'priority',
        'status',
        'resolved_at',
    ];

    public function wip()
    {
        return $this->belongsTo(Wip::class);
    }

    public function raisedBy()
    {
        return $this->belongsTo(User::class, 'raised_by_id');
    }

    public function getResolutionDurationStringAttribute()
    {
        if ($this->status !== 'Resolved' || !$this->resolved_at) {
            return null;
        }

        $start = \Carbon\Carbon::parse($this->created_at);
        $end = \Carbon\Carbon::parse($this->resolved_at);

        return $start->diffForHumans($end, [
            'parts' => 2,
            'short' => true,
            'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
        ]);
    }

    protected $appends = ['resolution_duration_string'];
}
