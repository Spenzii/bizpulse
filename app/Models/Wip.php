<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wip extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'company_id',
        'client_id',
        'assigned_to_id',
        'name',
        'description',
        'estimated_fee',
        'due_date',
        'completed_at',
        'status',
        'next_action',
        'billing_status',
        'is_recurring',
        'completion_note',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'is_recurring' => 'boolean',
        'estimated_fee' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function blockers()
    {
        return $this->hasMany(Blocker::class);
    }

    public function updates()
    {
        return $this->hasMany(ProgressUpdate::class);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
