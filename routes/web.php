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

    // ============ Kas & Bank ============
    Route::prefix('cash')->name('cash.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CashTransactionController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\CashTransactionController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\CashTransactionController::class, 'store'])->name('store');
        Route::get('/{cash}', [\App\Http\Controllers\CashTransactionController::class, 'show'])->name('show');
        Route::get('/{cash}/edit', [\App\Http\Controllers\CashTransactionController::class, 'edit'])->name('edit');
        Route::put('/{cash}', [\App\Http\Controllers\CashTransactionController::class, 'update'])->name('update');
        Route::delete('/{cash}', [\App\Http\Controllers\CashTransactionController::class, 'destroy'])->name('destroy');
    });

    // ============ Transfer Antar Akun ============
    Route::prefix('transfers')->name('transfers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TransferController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\TransferController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\TransferController::class, 'store'])->name('store');
        Route::get('/{transfer}', [\App\Http\Controllers\TransferController::class, 'show'])->name('show');
        Route::get('/{transfer}/edit', [\App\Http\Controllers\TransferController::class, 'edit'])->name('edit');
        Route::put('/{transfer}', [\App\Http\Controllers\TransferController::class, 'update'])->name('update');
        Route::delete('/{transfer}', [\App\Http\Controllers\TransferController::class, 'destroy'])->name('destroy');
    });

    // ============ Customer ============
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);

    // ============ Penjualan ============
    Route::resource('sales', \App\Http\Controllers\SaleController::class);
    Route::get('/sales/{sale}/payment', [\App\Http\Controllers\SalePaymentController::class, 'create'])->name('sales.payment.create');
    Route::post('/sales/{sale}/payment', [\App\Http\Controllers\SalePaymentController::class, 'store'])->name('sales.payment.store');

    // ============ Supplier ============
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

    // ============ Pembelian ============
    Route::resource('purchases', \App\Http\Controllers\PurchaseController::class);
    Route::get('/purchases/{purchase}/payment', [\App\Http\Controllers\PurchasePaymentController::class, 'create'])->name('purchases.payment.create');
    Route::post('/purchases/{purchase}/payment', [\App\Http\Controllers\PurchasePaymentController::class, 'store'])->name('purchases.payment.store');

    // ============ Laporan ============
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/ledger', [ReportController::class, 'ledger'])->name('ledger');
        Route::get('/trial-balance', [ReportController::class, 'trialBalance'])->name('trial-balance');
        Route::get('/income-statement', [ReportController::class, 'incomeStatement'])->name('income-statement');
        Route::get('/balance-sheet', [ReportController::class, 'balanceSheet'])->name('balance-sheet');
    });

    // ============ Aset Tetap ============
    Route::resource('fixed-assets', \App\Http\Controllers\FixedAssetController::class);
    Route::post('/fixed-assets/{fixedAsset}/dispose', [\App\Http\Controllers\FixedAssetController::class, 'dispose'])->name('fixed-assets.dispose');

    // Depresiasi (nama prefix fixed-assets.*)
    Route::prefix('depreciations')->name('fixed-assets.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AssetDepreciationController::class, 'index'])->name('depreciations');
        Route::get('/run', [\App\Http\Controllers\AssetDepreciationController::class, 'form'])->name('depreciation-run');
        Route::post('/run', [\App\Http\Controllers\AssetDepreciationController::class, 'run'])->name('depreciation-run.store');
    });

    // ============ Pajak ============
    Route::prefix('tax')->name('tax.')->group(function () {
        Route::get('/', [\App\Http\Controllers\TaxReportController::class, 'index'])->name('index');
        Route::get('/ppn', [\App\Http\Controllers\TaxReportController::class, 'ppn'])->name('ppn');
        Route::get('/pph23', [\App\Http\Controllers\TaxReportController::class, 'pph23'])->name('pph23');
        Route::get('/summary', [\App\Http\Controllers\TaxReportController::class, 'summary'])->name('summary');
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