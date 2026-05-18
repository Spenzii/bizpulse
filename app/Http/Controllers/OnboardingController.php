<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Models\Client;
use App\Models\Wip;
use App\Models\ProgressUpdate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class OnboardingController extends Controller
{
    /**
     * Display the onboarding wizard.
     */
    public function index(): Response|RedirectResponse
    {
        $company = Auth::user()->company;

        if ($company && $company->onboarded) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Onboarding', [
            'company' => $company,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Complete the onboarding wizard.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            abort(403, 'User does not belong to a company.');
        }

        $request->validate([
            // Company details
            'phone' => 'nullable|string|max:20',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address_line_1' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            
            // Industry Template
            'industry_type' => 'required|string|in:General SME,Accounting Firm,Construction Company,ICT Support Company,Legal Services,Security Services,NGO / Project Team,Logistics / Field Operations',
            
            // Staff
            'staff' => 'nullable|array',
            'staff.*.name' => 'required_with:staff.*.email|string|max:255',
            'staff.*.email' => 'required_with:staff.*.name|email|max:255',
            
            // First Client
            'client_name' => 'required|string|max:255',
            'client_sector' => 'nullable|string|max:255',
            
            // First WIP/Job
            'wip_name' => 'required|string|max:255',
            'wip_priority' => 'required|string|in:critical,high,medium,low',
            'wip_estimated_fee' => 'nullable|numeric|min:0',
            'wip_due_date' => 'required|date|after_or_equal:today',
            'wip_next_action' => 'required|string|max:255',
        ]);

        // 1. Update company profile
        $company->update([
            'address_line_1' => $request->address_line_1,
            'tax_id' => $request->tax_id,
            'industry_type' => $request->industry_type,
            'onboarded' => true,
        ]);

        // 2. Invite/Create Staff
        $createdStaffIds = [];
        if ($request->staff && is_array($request->staff)) {
            foreach ($request->staff as $staffData) {
                if (empty($staffData['name']) || empty($staffData['email'])) {
                    continue;
                }
                
                // Prevent duplicate user inside this check
                if (User::where('email', $staffData['email'])->exists()) {
                    continue;
                }

                $newStaff = User::create([
                    'name' => $staffData['name'],
                    'email' => $staffData['email'],
                    'password' => Hash::make('Password123!'), // Default password for initial staff login
                    'company_id' => $company->id,
                    'role' => 'staff',
                ]);
                $createdStaffIds[] = $newStaff->id;
            }
        }

        // 3. Create First Client
        $client = Client::create([
            'company_id' => $company->id,
            'name' => $request->client_name,
            'contact_person' => 'Primary Contact',
            'relationship_owner_id' => $user->id,
            'status' => 'Active',
            'next_follow_up_date' => now()->addDays(7),
        ]);

        // 4. Create First WIP / Job
        $assigneeId = !empty($createdStaffIds) ? $createdStaffIds[0] : $user->id;

        $wip = Wip::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'assigned_to_id' => $assigneeId,
            'name' => $request->wip_name,
            'description' => 'Logged during company onboarding setup.',
            'estimated_fee' => $request->wip_estimated_fee ?? 0.00,
            'due_date' => $request->wip_due_date,
            'status' => 'Active',
            'next_action' => $request->wip_next_action,
            'billing_status' => 'Not Ready',
        ]);

        // Log first update on first WIP
        ProgressUpdate::create([
            'company_id' => $company->id,
            'wip_id' => $wip->id,
            'user_id' => $user->id,
            'content' => 'Job successfully logged and assigned during onboarding wizard setup.',
            'percentage_completed' => 10,
        ]);

        // 5. Seed default task templates based on industry (WIP templates to play with)
        $this->seedIndustryTemplates($company, $client, $user);

        return redirect()->route('dashboard')->with('success', 'Onboarding completed successfully! Welcome to BizPulse.');
    }

    /**
     * Seeds helper templates / default tasks for the company
     */
    protected function seedIndustryTemplates(Company $company, Client $client, User $user): void
    {
        $templates = [
            'General SME' => [
                ['name' => 'Complete Business Registration Extract', 'fee' => 1200, 'action' => 'Submit extract to bank'],
                ['name' => 'Quote preparation for new prospect', 'fee' => 0, 'action' => 'Draft pricing PDF and email client'],
            ],
            'Accounting Firm' => [
                ['name' => 'Monthly GST Filing', 'fee' => 1500, 'action' => 'Compile client sales journals'],
                ['name' => 'Annual Income Tax Return 2025', 'fee' => 4500, 'action' => 'Reconcile draft balance sheet'],
            ],
            'Construction Company' => [
                ['name' => 'Initial Site Inspection & Safety Check', 'fee' => 3000, 'action' => 'Complete site safety checklist'],
                ['name' => 'Subcontractor Agreement Review', 'fee' => 0, 'action' => 'Send signed agreements'],
            ],
            'ICT Support Company' => [
                ['name' => 'Server & Email Infrastructure Setup', 'fee' => 2500, 'action' => 'Map domain DNS and create mailboxes'],
                ['name' => 'Website Content Management Updates', 'fee' => 800, 'action' => 'Update staff bios and service copy'],
            ],
            'Legal Services' => [
                ['name' => 'Draft Shareholder Engagement Contract', 'fee' => 5000, 'action' => 'Prepare first draft for partner review'],
                ['name' => 'Client Legal Consultation Meeting', 'fee' => 1200, 'action' => 'Schedule booking follow-up'],
            ],
            'Security Services' => [
                ['name' => 'Patrol Route Configuration Plan', 'fee' => 1800, 'action' => 'Map physical guard checkpoints'],
                ['name' => 'Staff Deployment Briefing', 'fee' => 0, 'action' => 'Issue gear and roster'],
            ],
            'NGO / Project Team' => [
                ['name' => 'Quarterly Stakeholder Progress Report', 'fee' => 0, 'action' => 'Compile fieldwork photographs and logs'],
                ['name' => 'Grant Proposal Submission', 'fee' => 0, 'action' => 'Finalize budget spreadsheet'],
            ],
            'Logistics / Field Operations' => [
                ['name' => 'Fleet Vehicle Safety Maintenance Review', 'fee' => 1500, 'action' => 'Schedule brake and tire check'],
                ['name' => 'Warehouse Inventory Reconciliation Audit', 'fee' => 2000, 'action' => 'Count stock variances in block B'],
            ],
        ];

        $industry = $company->industry_type;
        $tasks = $templates[$industry] ?? $templates['General SME'];

        foreach ($tasks as $task) {
            Wip::create([
                'company_id' => $company->id,
                'client_id' => $client->id,
                'assigned_to_id' => $user->id,
                'name' => $task['name'],
                'description' => 'Suggested setup template for your sector.',
                'estimated_fee' => $task['fee'],
                'due_date' => now()->addDays(14),
                'status' => 'Active',
                'next_action' => $task['action'],
                'billing_status' => 'Not Ready',
            ]);
        }
    }
}
