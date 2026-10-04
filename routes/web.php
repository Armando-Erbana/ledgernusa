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
        // ============ Persediaan ============
    Route::resource('product-categories', \App\Http\Controllers\ProductCategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('products', \App\Http\Controllers\ProductController::class);
    Route::resource('warehouses', \App\Http\Controllers\WarehouseController::class)->except(['create', 'show', 'edit']);
    Route::resource('stock', \App\Http\Controllers\StockMovementController::class)->except(['show', 'edit', 'update']);

        Route::prefix('reports')->name('reports.')->group(function () {
        // ... route yang sudah ada
        Route::get('/aging-piutang', [\App\Http\Controllers\AgingReportController::class, 'piutang'])->name('aging-piutang');
        Route::get('/aging-hutang', [\App\Http\Controllers\AgingReportController::class, 'hutang'])->name('aging-hutang');
    });

        // Export
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/income-statement-pdf', [\App\Http\Controllers\ExportController::class, 'incomeStatementPdf'])->name('income-statement-pdf');
        Route::get('/balance-sheet-pdf', [\App\Http\Controllers\ExportController::class, 'balanceSheetPdf'])->name('balance-sheet-pdf');
        Route::get('/trial-balance-pdf', [\App\Http\Controllers\ExportController::class, 'trialBalancePdf'])->name('trial-balance-pdf');
        Route::get('/sales-excel', [\App\Http\Controllers\ExportController::class, 'salesExcel'])->name('sales-excel');
        Route::get('/purchases-excel', [\App\Http\Controllers\ExportController::class, 'purchasesExcel'])->name('purchases-excel');
    });
    
    // Company
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/switch/{id}', [CompanyController::class, 'switch'])->name('companies.switch');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // ============ Rekonsiliasi Bank ============
    Route::prefix('bank-rec')->name('bank-rec.')->group(function () {
        Route::get('/', [\App\Http\Controllers\BankReconciliationController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\BankReconciliationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\BankReconciliationController::class, 'store'])->name('store');
        Route::get('/{bankRec}', [\App\Http\Controllers\BankReconciliationController::class, 'show'])->name('show');
        Route::post('/{bankRec}/auto-match', [\App\Http\Controllers\BankReconciliationController::class, 'autoMatch'])->name('auto-match');
        Route::post('/{bankRec}/lines/{line}/match', [\App\Http\Controllers\BankReconciliationController::class, 'matchLine'])->name('lines.match');
        Route::post('/{bankRec}/lines/{line}/unmatch', [\App\Http\Controllers\BankReconciliationController::class, 'unmatchLine'])->name('lines.unmatch');
        Route::post('/{bankRec}/lines/{line}/exclude', [\App\Http\Controllers\BankReconciliationController::class, 'excludeLine'])->name('lines.exclude');
        Route::post('/{bankRec}/finalize', [\App\Http\Controllers\BankReconciliationController::class, 'finalize'])->name('finalize');
        Route::delete('/{bankRec}', [\App\Http\Controllers\BankReconciliationController::class, 'destroy'])->name('destroy');
    });

        // ============ Sales Order ============
    Route::resource('sales-orders', \App\Http\Controllers\SalesOrderController::class);
    Route::post('/sales-orders/{salesOrder}/confirm', [\App\Http\Controllers\SalesOrderController::class, 'confirm'])->name('sales-orders.confirm');

    // ============ Delivery Order ============
    Route::resource('delivery-orders', \App\Http\Controllers\DeliveryOrderController::class)->except(['edit', 'update']);
    Route::post('/delivery-orders/{deliveryOrder}/deliver', [\App\Http\Controllers\DeliveryOrderController::class, 'deliver'])->name('delivery-orders.deliver');
    Route::post('/delivery-orders/{deliveryOrder}/create-invoice', [\App\Http\Controllers\DeliveryOrderController::class, 'createInvoice'])->name('delivery-orders.create-invoice');

        // ============ Purchase Order ============
    Route::resource('purchase-orders', \App\Http\Controllers\PurchaseOrderController::class);
    Route::post('/purchase-orders/{purchaseOrder}/confirm', [\App\Http\Controllers\PurchaseOrderController::class, 'confirm'])->name('purchase-orders.confirm');

    // ============ Goods Receipt ============
    Route::resource('goods-receipts', \App\Http\Controllers\GoodsReceiptController::class)->except(['edit', 'update']);
    Route::post('/goods-receipts/{goodsReceipt}/receive', [\App\Http\Controllers\GoodsReceiptController::class, 'receive'])->name('goods-receipts.receive');
    Route::post('/goods-receipts/{goodsReceipt}/create-bill', [\App\Http\Controllers\GoodsReceiptController::class, 'createBill'])->name('goods-receipts.create-bill');
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