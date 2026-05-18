<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'amount',
        'currency',
        'payment_date',
        'payment_method',
        'reference_number',
        'status',
        'notes',
        'type', // Subscription, Setup Fee, Training Fee, Custom
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
