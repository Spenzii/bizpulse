<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wip;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function relationshipOwner()
    {
        // Only company admins can see this report
        if (Auth::user()->role !== 'company_admin') {
            abort(403);
        }

        $companyId = Auth::user()->company_id;
        
        $reportData = User::where('company_id', $companyId)
            ->whereIn('role', ['staff', 'company_admin'])
            ->withCount([
                'assignedWips as total_jobs',
                'assignedWips as active_jobs' => function ($query) {
                    $query->where('status', 'Active');
                },
                'assignedWips as blocked_jobs' => function ($query) {
                    $query->where('status', 'Blocked');
                },
                'assignedWips as completed_jobs' => function ($query) {
                    $query->where('status', 'Completed');
                }
            ])
            ->get()
            ->map(function ($user) {
                // Calculate Revenue Exposure (WIPs marked Ready to Invoice)
                $user->revenue_at_risk = Wip::where('assigned_to_id', $user->id)
                    ->where('billing_status', 'Ready to Invoice')
                    ->sum('estimated_fee');

                // Completion Velocity: Avg days taken to complete jobs
                $completedWips = Wip::where('assigned_to_id', $user->id)
                    ->where('status', 'Completed')
                    ->whereNotNull('completed_at')
                    ->get();
                
                $totalDays = 0;
                foreach ($completedWips as $wip) {
                    $completed = Carbon::parse($wip->completed_at);
                    $created = Carbon::parse($wip->created_at);
                    $totalDays += abs($completed->diffInDays($created));
                }
                $user->avg_completion_days = $completedWips->count() > 0 
                    ? round($totalDays / $completedWips->count(), 1) 
                    : null;

                // Fee Realization Rate (% of actual invoiced vs original estimated fee)
                $invoicedWips = Wip::where('assigned_to_id', $user->id)
                    ->where('status', 'Completed')
                    ->where('billing_status', 'Invoiced')
                    ->with('invoiceItems')
                    ->get();
                
                $totalEstimated = 0;
                $totalInvoiced = 0;
                foreach ($invoicedWips as $wip) {
                    $totalEstimated += (float)$wip->estimated_fee;
                    $totalInvoiced += (float)$wip->invoiceItems->sum('amount');
                }
                
                $user->realization_rate = $totalEstimated > 0 
                    ? round(($totalInvoiced / $totalEstimated) * 100, 1) 
                    : null;

                return $user;
            });

        return Inertia::render('Reports/RelationshipOwner', [
            'reportData' => $reportData,
        ]);
    }

    public function financial()
    {
        // Only company admins can see this report
        if (Auth::user()->role !== 'company_admin') {
            abort(403);
        }

        $companyId = Auth::user()->company_id;
        $now = now();

        // 1. Key Metrics Overview
        $totalBilled = Invoice::where('company_id', $companyId)
            ->whereIn('status', ['Sent', 'Paid'])
            ->sum('total_amount');

        $totalCollected = Invoice::where('company_id', $companyId)
            ->where('status', 'Paid')
            ->sum('total_amount');

        $outstandingReceivables = Invoice::where('company_id', $companyId)
            ->where('status', 'Sent')
            ->sum('total_amount');

        $collectedGst = Invoice::where('company_id', $companyId)
            ->where('status', 'Paid')
            ->sum('tax_amount');

        $taxLiability = Invoice::where('company_id', $companyId)
            ->where('status', 'Sent')
            ->sum('tax_amount');

        // WIP Forecasting Pipeline
        $readyToInvoiceWipValue = Wip::where('company_id', $companyId)
            ->where('billing_status', 'Ready to Invoice')
            ->sum('estimated_fee');

        $activeWipPipeline = Wip::where('company_id', $companyId)
            ->whereIn('status', ['Active', 'Blocked'])
            ->sum('estimated_fee');

        $totalForecast = $totalBilled + $readyToInvoiceWipValue + $activeWipPipeline;

        // 2. Aged Receivables (Aging Buckets based on created_at for 'Sent' status)
        $agingBuckets = [
            'current' => Invoice::where('company_id', $companyId)->where('status', 'Sent')->where('created_at', '>=', $now->copy()->subDays(30))->sum('total_amount'),
            'bracket_30_60' => Invoice::where('company_id', $companyId)->where('status', 'Sent')->where('created_at', '<', $now->copy()->subDays(30))->where('created_at', '>=', $now->copy()->subDays(60))->sum('total_amount'),
            'bracket_60_90' => Invoice::where('company_id', $companyId)->where('status', 'Sent')->where('created_at', '<', $now->copy()->subDays(60))->where('created_at', '>=', $now->copy()->subDays(90))->sum('total_amount'),
            'bracket_90_plus' => Invoice::where('company_id', $companyId)->where('status', 'Sent')->where('created_at', '<', $now->copy()->subDays(90))->sum('total_amount'),
        ];

        // 3. 6-Month Billing Trend (Historical)
        $billingTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = $now->copy()->subMonths($i);
            $start = $monthDate->copy()->startOfMonth();
            $end = $monthDate->copy()->endOfMonth();

            $monthlyBilled = Invoice::where('company_id', $companyId)
                ->whereIn('status', ['Sent', 'Paid'])
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_amount');

            $billingTrend[] = [
                'month' => $monthDate->format('M Y'),
                'amount' => (float)$monthlyBilled,
            ];
        }

        // 4. Detailed Aging Ledger (Outstanding Sent Invoices with Overdue Indicators)
        $outstandingLedger = Invoice::where('company_id', $companyId)
            ->where('status', 'Sent')
            ->with('client')
            ->oldest()
            ->get()
            ->map(function ($invoice) use ($now) {
                $dueDate = Carbon::parse($invoice->due_date);
                $invoice->days_overdue = $now->greaterThan($dueDate) ? (int)$now->diffInDays($dueDate) : 0;
                return $invoice;
            });

        return Inertia::render('Reports/Financial', [
            'metrics' => [
                'total_billed' => (float)$totalBilled,
                'total_collected' => (float)$totalCollected,
                'outstanding_receivables' => (float)$outstandingReceivables,
                'collected_gst' => (float)$collectedGst,
                'tax_liability' => (float)$taxLiability,
                'ready_to_invoice_wip_value' => (float)$readyToInvoiceWipValue,
                'active_wip_pipeline' => (float)$activeWipPipeline,
                'total_forecast' => (float)$totalForecast,
            ],
            'agingBuckets' => $agingBuckets,
            'billingTrend' => $billingTrend,
            'outstandingLedger' => $outstandingLedger,
        ]);
    }
}
