<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Wip;
use App\Models\Blocker;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'client') {
            return redirect()->route('client-portal.dashboard');
        }

        if ($user->role === 'super_admin') {
            $now = now();
            $monthStart = $now->copy()->startOfMonth();

            return Inertia::render('Admin/Dashboard', [
                'stats' => [
                    'total_companies' => Company::count(),
                    'active_companies' => Company::where('subscription_status', 'Active')->count(),
                    'trial_companies' => Company::where('subscription_status', 'Trial')->count(),
                    'overdue_companies' => Company::where('subscription_status', 'Overdue')->count(),
                    'suspended_companies' => Company::where('subscription_status', 'Suspended')->count(),
                    'cancelled_companies' => Company::where('subscription_status', 'Cancelled')->count(),
                    
                    'total_users' => \App\Models\User::count(),
                    'active_users_this_month' => \App\Models\User::where('last_login_at', '>=', $monthStart)->count(),
                    
                    'monthly_recurring_revenue' => \App\Models\Payment::where('type', 'Subscription')
                        ->where('payment_date', '>=', $monthStart)
                        ->sum('amount'),
                    'setup_fees_collected' => \App\Models\Payment::where('type', 'Setup Fee')->sum('amount'),
                    'payments_received_this_month' => \App\Models\Payment::where('payment_date', '>=', $monthStart)->sum('amount'),
                    'outstanding_payments' => \App\Models\Payment::where('status', 'Pending')->sum('amount'),
                    
                    'support_tickets_open' => \App\Models\SupportTicket::where('status', 'Open')->count(),
                    'new_signups_this_month' => Company::where('created_at', '>=', $monthStart)->count(),
                    'companies_due_for_renewal' => Company::where('trial_ends_at', '<=', $now->copy()->addDays(7))->count(),
                    
                    'website_leads' => \App\Models\Lead::where('source', 'Contact')->count(),
                    'demo_requests' => \App\Models\Lead::where('source', 'Demo')->count(),
                    
                    // Note: Storage used might require a custom service or disk check, returning 0 for now
                    'storage_used' => 0, 
                ]
            ]);
        }

        if ($user->role === 'company_admin') {
            $now = now();
            $thisMonthStart = $now->copy()->startOfMonth();
            $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
            $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

            return Inertia::render('Dashboard', [
                'role' => 'company_admin',
                'stats' => [
                    'active_wips' => Wip::where('status', 'Active')->count(),
                    'overdue_wips' => Wip::where('status', 'Active')->where('due_date', '<', now())->count(),
                    'open_blockers' => Blocker::where('status', 'Open')->count(),
                    'revenue_at_risk' => Wip::where('billing_status', 'Ready to Invoice')->sum('estimated_fee'),
                    
                    'billing_performance' => [
                        'current_month_billed' => Wip::where('status', 'Completed')
                            ->where('completed_at', '>=', $thisMonthStart)
                            ->sum('estimated_fee'),
                        'last_month_billed' => Wip::where('status', 'Completed')
                            ->whereBetween('completed_at', [$lastMonthStart, $lastMonthEnd])
                            ->sum('estimated_fee'),
                        'pipeline_value' => Wip::whereIn('status', ['Active', 'Blocked'])
                            ->sum('estimated_fee'),
                        'target_billed' => 10000, // Hardcoded for now as requested in implementation plan
                    ]
                ],
                'recent_wips' => Wip::with('client', 'assignedTo')->latest()->take(5)->get(),
            ]);
        }

        // Staff View
        return Inertia::render('Dashboard', [
            'role' => 'staff',
            'my_tasks' => Wip::where('assigned_to_id', $user->id)
                ->with('client')
                ->whereIn('status', ['Active', 'Blocked'])
                ->get(),
        ]);
    }
}
