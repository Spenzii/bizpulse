<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'primary_color',
        'secondary_color',
        'address_line_1',
        'address_line_2',
        'tax_id',
        'subscription_plan',
        'subscription_status',
        'trial_ends_at',
        'setup_fee_paid',
        'onboarded',
        'industry_type',
    ];

    protected $casts = [
        'onboarded' => 'boolean',
        'setup_fee_paid' => 'boolean',
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Get the users for the company.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

