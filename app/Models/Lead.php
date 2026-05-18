<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'message',
        'status', // New, Contacted, Demo Scheduled, Closed
        'source', // Website, Referral, etc.
    ];
}
