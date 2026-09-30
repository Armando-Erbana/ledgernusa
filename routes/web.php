<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\SuperAdmin\TenantController as SuperAdminTenant;
use Illuminate\Support\Facades\Route;

// =========================================================
// ROOT
// =========================================================
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

// =========================================================
// GRUP 1: Auth + Active Company (TANPA cek subscription)
// Bisa diakses walau langganan expired
// =========================================================
Route::middleware(['auth', 'active.company'])->group(function () {

    // Subscription
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');

    // Company
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/switch/{id}', [CompanyController::class, 'switch'])->name('companies.switch');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =========================================================
// GRUP 2: Auth + Active Company + Subscription check
// Fitur utama aplikasi
// =========================================================
Route::middleware(['auth', 'active.company', 'subscription'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // AI Assistant
    Route::post('/ai/chat', [AiAssistantController::class, 'chat'])->name('ai.chat');

    // ============ COA ============
    Route::get('/accounts/import', [AccountController::class, 'importForm'])->name('accounts.import');
    Route::post('/accounts/import', [AccountController::class, 'import'])->name('accounts.import.store');
    Route::resource('accounts', AccountController::class);

    // ============ Kontak ============
    Route::resource('contacts', ContactController::class);

    // ============ Jurnal ============
    Route::resource('journals', JournalController::class);
    Route::post('/journals/{journal}/post', [JournalController::class, 'post'])
        ->middleware('role:owner,admin')
        ->name('journals.post');

    // ============ Laporan ============
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/ledger', [ReportController::class, 'ledger'])->name('ledger');
        Route::get('/trial-balance', [ReportController::class, 'trialBalance'])->name('trial-balance');
        Route::get('/income-statement', [ReportController::class, 'incomeStatement'])->name('income-statement');
        Route::get('/balance-sheet', [ReportController::class, 'balanceSheet'])->name('balance-sheet');
    });
});

// =========================================================
// SUPER ADMIN ROUTES
// =========================================================
Route::middleware(['auth', 'super.admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::get('/', [SuperAdminDashboard::class, 'index'])->name('dashboard');

        Route::get('/tenants', [SuperAdminTenant::class, 'index'])->name('tenants.index');
        Route::get('/tenants/{tenant}', [SuperAdminTenant::class, 'show'])->name('tenants.show');

        Route::post('/tenants/{tenant}/activate', [SuperAdminTenant::class, 'activate'])->name('tenants.activate');
        Route::post('/tenants/{tenant}/extend', [SuperAdminTenant::class, 'extend'])->name('tenants.extend');
        Route::post('/tenants/{tenant}/change-plan', [SuperAdminTenant::class, 'changePlan'])->name('tenants.change-plan');
        Route::post('/tenants/{tenant}/suspend', [SuperAdminTenant::class, 'suspend'])->name('tenants.suspend');
        Route::post('/tenants/{tenant}/reactivate', [SuperAdminTenant::class, 'reactivate'])->name('tenants.reactivate');
    });

require __DIR__.'/auth.php';