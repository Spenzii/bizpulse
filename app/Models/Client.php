<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'relationship_owner_id',
        'status',
        'next_follow_up_date',
        'is_archived',
    ];

    public function relationshipOwner()
    {
        return $this->belongsTo(User::class, 'relationship_owner_id');
    }

    public function wips()
    {
        return $this->hasMany(Wip::class);
    }
}
