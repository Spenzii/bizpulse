<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CompanyController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'onboarded'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Onboarding Routes (Accessible by authenticated users before completing setup)
    Route::get('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'store'])->name('onboarding.store');

    // Client Portal Routes (Portal specific)
    Route::get('/portal', [\App\Http\Controllers\ClientPortalController::class, 'dashboard'])->name('client-portal.dashboard');
    Route::get('/portal/wips/{wip}', [\App\Http\Controllers\ClientPortalController::class, 'showWip'])->name('client-portal.wips.show');
    
    // Invoices (Shared Access, authorization in controller)
    Route::get('invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
    
    Route::middleware(['staff', 'onboarded'])->group(function () {
        Route::post('wips/bulk-assign', [\App\Http\Controllers\WipController::class, 'bulkAssign'])->name('wips.bulk-assign');
        Route::post('wips/bulk-duplicate', [\App\Http\Controllers\WipController::class, 'bulkDuplicate'])->name('wips.bulk-duplicate');
        Route::resource('wips', \App\Http\Controllers\WipController::class);
        
        Route::resource('clients', \App\Http\Controllers\ClientController::class);
        Route::post('clients/{client}/portal-users', [\App\Http\Controllers\ClientController::class, 'createPortalUser'])->name('clients.portal-users.store');
        
        Route::post('wips/{wip}/blockers', [\App\Http\Controllers\BlockerController::class, 'store'])->name('wips.blockers.store');
        Route::patch('blockers/{blocker}/resolve', [\App\Http\Controllers\BlockerController::class, 'resolve'])->name('blockers.resolve');
        Route::post('wips/{wip}/updates', [\App\Http\Controllers\WipUpdateController::class, 'store'])->name('wips.updates.store');
        Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
        
        // Reports
        Route::get('reports/relationship-owners', [\App\Http\Controllers\ReportController::class, 'relationshipOwner'])->name('reports.relationship-owners');
        Route::get('reports/financial', [\App\Http\Controllers\ReportController::class, 'financial'])->name('reports.financial');
        
        // Exports
        Route::get('exports/wips', [\App\Http\Controllers\ExportController::class, 'wips'])->name('exports.wips');
        Route::get('exports/relationship-owners', [\App\Http\Controllers\ExportController::class, 'relationshipOwners'])->name('exports.relationship-owners');
        Route::get('exports/financial', [\App\Http\Controllers\ExportController::class, 'financial'])->name('exports.financial');
        Route::get('exports/xero', [\App\Http\Controllers\ExportController::class, 'xero'])->name('exports.xero');
        
        // Weekly Reviews
        Route::resource('weekly-reviews', \App\Http\Controllers\WeeklyReviewController::class);
        Route::get('/planner', [App\Http\Controllers\PlannerController::class, 'index'])->name('planner.index');
        
        // Company Billing
        Route::get('billing', [\App\Http\Controllers\CompanyBillingController::class, 'index'])->name('company-billing.index');
        Route::patch('billing/{wip}', [\App\Http\Controllers\CompanyBillingController::class, 'updateStatus'])->name('company-billing.update');
        
        // Invoices Management
        Route::get('invoices', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('invoices', [\App\Http\Controllers\InvoiceController::class, 'store'])->name('invoices.store');
        Route::patch('invoices/{invoice}/status', [\App\Http\Controllers\InvoiceController::class, 'updateStatus'])->name('invoices.status.update');

        // Company Settings & Branding
        Route::get('settings', [\App\Http\Controllers\CompanySettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\CompanySettingsController::class, 'update'])->name('settings.update');
        Route::post('invoices/{invoice}/send', [\App\Http\Controllers\InvoiceController::class, 'send'])->name('invoices.send');

        // SOP & Help Hub
        Route::get('operating-rhythm', [\App\Http\Controllers\SopController::class, 'index'])->name('operating-rhythm.index');
    });
});

Route::middleware(['auth', 'super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('companies', CompanyController::class);
    Route::post('companies/{company}/users', [\App\Http\Controllers\Admin\CompanyUserController::class, 'store'])->name('companies.users.store');
    
    // Website CMS
    Route::get('cms', [\App\Http\Controllers\Admin\CmsController::class, 'index'])->name('cms.index');
    Route::patch('cms/sections/{section}', [\App\Http\Controllers\Admin\CmsController::class, 'updateSection'])->name('cms.sections.update');
    Route::patch('cms/seo/{seo}', [\App\Http\Controllers\Admin\CmsController::class, 'updateSeo'])->name('cms.seo.update');
    
    // Subscriptions & Payments
    Route::resource('subscription-plans', \App\Http\Controllers\Admin\SubscriptionPlanController::class);
    Route::resource('payments', \App\Http\Controllers\Admin\PaymentController::class);
    
    // Support & Leads
    Route::get('leads', [\App\Http\Controllers\Admin\SupportController::class, 'leads'])->name('leads.index');
    Route::get('demo-requests', [\App\Http\Controllers\Admin\SupportController::class, 'demoRequests'])->name('demo-requests.index');
    Route::resource('tickets', \App\Http\Controllers\Admin\SupportController::class)->only(['index', 'show', 'update']);
    
    // System
    Route::get('audit-logs', [\App\Http\Controllers\Admin\SystemController::class, 'auditLogs'])->name('audit-logs.index');
    Route::get('settings', [\App\Http\Controllers\Admin\SystemController::class, 'settings'])->name('settings.index');
});

require __DIR__.'/auth.php';
