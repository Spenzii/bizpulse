<?php

namespace App\Observers;

use App\Models\Wip;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class WipObserver
{
    public function created(Wip $wip): void
    {
        AuditLog::create([
            'company_id' => $wip->company_id,
            'user_id' => Auth::id(),
            'event' => 'Job Created',
            'auditable_type' => Wip::class,
            'auditable_id' => $wip->id,
            'new_values' => $wip->getAttributes(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public function updated(Wip $wip): void
    {
        $changes = $wip->getChanges();
        
        // Handle completed_at logic
        if (isset($changes['status'])) {
            if ($changes['status'] === 'Completed') {
                $wip->updateQuietly(['completed_at' => now()]);
            } else {
                $wip->updateQuietly(['completed_at' => null]);
            }
            // Refresh changes after updateQuietly? No, getChanges() is already calculated.
        }

        // Filter changes we care about for audit
        $trackedFields = ['status', 'assigned_to_id', 'due_date', 'estimated_fee', 'completed_at', 'is_recurring'];
        $filteredChanges = array_intersect_key($changes, array_flip($trackedFields));

        if (empty($filteredChanges)) {
            return;
        }

        $oldValues = array_intersect_key($wip->getOriginal(), array_flip(array_keys($filteredChanges)));

        AuditLog::create([
            'company_id' => $wip->company_id,
            'user_id' => Auth::id(),
            'event' => 'Job Updated',
            'auditable_type' => Wip::class,
            'auditable_id' => $wip->id,
            'old_values' => $oldValues,
            'new_values' => $filteredChanges,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
