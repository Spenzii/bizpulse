<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function wips()
    {
        $companyId = Auth::user()->company_id;
        $wips = Wip::where('company_id', $companyId)->with('client', 'assignedTo')->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=wip_tracker_export_' . now()->format('Y-m-d') . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $columns = ['Client', 'WIP Name', 'Assigned To', 'Status', 'Due Date', 'Estimated Fee', 'Billing Status'];

        $callback = function() use($wips, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($wips as $wip) {
                fputcsv($file, [
                    $wip->client->name,
                    $wip->name,
                    $wip->assignedTo->name ?? 'Unassigned',
                    $wip->status,
                    $wip->due_date->format('Y-m-d'),
                    $wip->estimated_fee,
                    $wip->billing_status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function relationshipOwners()
    {
        if (Auth::user()->role !== 'company_admin') {
            abort(403);
        }

        $companyId = Auth::user()->company_id;
        $users = User::where('company_id', $companyId)
            ->whereIn('role', ['staff', 'company_admin'])
            ->withCount([
                'assignedWips as total_jobs',
                'assignedWips as active_jobs' => function ($query) { $query->where('status', 'Active'); },
                'assignedWips as blocked_jobs' => function ($query) { $query->where('status', 'Blocked'); },
                'assignedWips as completed_jobs' => function ($query) { $query->where('status', 'Completed'); }
            ])
            ->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=relationship_owner_report_' . now()->format('Y-m-d') . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $columns = ['Staff Member', 'Email', 'Total Jobs', 'Active', 'Blocked', 'Completed', 'Revenue Exposure'];

        $callback = function() use($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                $revenueAtRisk = Wip::where('assigned_to_id', $user->id)
                    ->where('billing_status', 'Ready to Invoice')
                    ->sum('estimated_fee');

                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $user->total_jobs,
                    $user->active_jobs,
                    $user->blocked_jobs,
                    $user->completed_jobs,
                    $revenueAtRisk
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function financial()
    {
        if (Auth::user()->role !== 'company_admin') {
            abort(403);
        }

        $companyId = Auth::user()->company_id;
        $invoices = \App\Models\Invoice::where('company_id', $companyId)->with('client')->latest()->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=financial_invoice_report_' . now()->format('Y-m-d') . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $columns = ['Invoice Number', 'Client Name', 'Subtotal (PGK)', 'Tax/GST (PGK)', 'Total (PGK)', 'Status', 'Issue Date', 'Due Date', 'Age Category'];

        $callback = function() use ($invoices, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $now = now();
            foreach ($invoices as $invoice) {
                $subtotal = $invoice->total_amount - $invoice->tax_amount;
                $created = \Carbon\Carbon::parse($invoice->created_at);
                
                $ageCategory = 'N/A';
                if ($invoice->status === 'Sent') {
                    $diff = $now->diffInDays($created);
                    if ($diff <= 30) {
                        $ageCategory = '0-30 Days';
                    } elseif ($diff <= 60) {
                        $ageCategory = '31-60 Days';
                    } elseif ($diff <= 90) {
                        $ageCategory = '61-90 Days';
                    } else {
                        $ageCategory = '90+ Days';
                    }
                } elseif ($invoice->status === 'Paid') {
                    $ageCategory = 'Paid';
                }

                fputcsv($file, [
                    $invoice->invoice_number,
                    $invoice->client->name,
                    number_format($subtotal, 2, '.', ''),
                    number_format($invoice->tax_amount, 2, '.', ''),
                    number_format($invoice->total_amount, 2, '.', ''),
                    $invoice->status,
                    $created->format('Y-m-d'),
                    \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d'),
                    $ageCategory
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function xero()
    {
        if (Auth::user()->role !== 'company_admin') {
            abort(403);
        }

        $companyId = Auth::user()->company_id;
        $invoices = \App\Models\Invoice::where('company_id', $companyId)
            ->whereIn('status', ['Sent', 'Paid'])
            ->with(['client', 'items'])
            ->latest()
            ->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=xero_invoice_import_' . now()->format('Y-m-d') . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $columns = [
            '*ContactName',
            'EmailAddress',
            'POAddressLine1',
            '*InvoiceNumber',
            'Reference',
            '*InvoiceDate',
            '*DueDate',
            '*Description',
            '*Quantity',
            '*UnitAmount',
            '*AccountCode',
            '*TaxType',
            'TaxAmount'
        ];

        $callback = function() use ($invoices, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($invoices as $invoice) {
                $created = \Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d');
                $due = \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d');
                
                foreach ($invoice->items as $item) {
                    fputcsv($file, [
                       $invoice->client->name,
                       $invoice->client->email ?? '',
                       $invoice->client->phone ?? '',
                       $invoice->invoice_number,
                       'BizPulse Ref: ' . $invoice->id,
                       $created,
                       $due,
                       $item->description,
                       1,
                       number_format($item->amount, 2, '.', ''),
                       '200',
                       'GST on Income',
                       number_format($item->amount * 0.10, 2, '.', '')
                    ]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
