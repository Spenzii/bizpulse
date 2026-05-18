<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SystemController extends Controller
{
    public function auditLogs()
    {
        return Inertia::render('Admin/System/AuditLogs', [
            'logs' => AuditLog::with('user')->latest()->take(100)->get(),
        ]);
    }

    public function settings()
    {
        return Inertia::render('Admin/System/Settings');
    }
}
