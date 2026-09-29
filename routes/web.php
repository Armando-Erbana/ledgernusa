<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

// Root: redirect sesuai status login
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

// Debug route — HAPUS setelah masalah selesai
Route::get('/cek-session', function () {
    if (!auth()->check()) return 'Belum login';
    $companyId = session('company_id');
    return [
        'user_email' => auth()->user()->email,
        'session_company_id' => $companyId,
        'session_company_name' => $companyId ? \App\Models\Company::find($companyId)?->name : null,
        'user_companies' => auth()->user()->companies->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'role' => $c->pivot->role,
        ]),
    ];
});

// =========================================================
// GRUP 1: Auth + Active Company (TANPA cek subscription)
// Berisi route subscription itu sendiri + company + logout
// =========================================================
Route::middleware(['auth', 'active.company'])->group(function () {

    // Subscription — HARUS di luar middleware subscription (biar bisa diakses saat expired)
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');

    // Company — harus bisa diakses kapan saja
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
// Berisi fitur utama aplikasi
// =========================================================
Route::middleware(['auth', 'active.company', 'subscription'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ============ COA ============
    // Import HARUS di atas resource
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
        Route::get('/', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/tenants', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'index'])
            ->name('tenants.index');
        Route::get('/tenants/{tenant}', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'show'])
            ->name('tenants.show');

        Route::post('/tenants/{tenant}/activate', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'activate'])
            ->name('tenants.activate');
        Route::post('/tenants/{tenant}/extend', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'extend'])
            ->name('tenants.extend');
        Route::post('/tenants/{tenant}/change-plan', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'changePlan'])
            ->name('tenants.change-plan');
        Route::post('/tenants/{tenant}/suspend', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'suspend'])
            ->name('tenants.suspend');
        Route::post('/tenants/{tenant}/reactivate', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'reactivate'])
            ->name('tenants.reactivate');
    });
require __DIR__.'/auth.php';