<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpaController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\API\LoanController;
use App\Http\Controllers\API\MenuController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\UnitController;
use App\Http\Controllers\API\AssetController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\DomainController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\TenantController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\API\AccountController;
use App\Http\Controllers\API\BalanceController;
use App\Http\Controllers\API\ExpenseController;
use App\Http\Controllers\API\GeneralController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PayrollController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\VatRateController;
use App\Http\Controllers\TableExportController;
use App\Http\Controllers\API\CurrencyController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\PurchaseController;
use App\Http\Controllers\API\SupplierController;
use App\Http\Controllers\PDFGeneratorController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\API\AssetTypeController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\ExpSubCatController;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\ProSubCatController;
use App\Http\Controllers\API\QuotationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\ExpenseCatController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RichEditorController;
use App\Http\Controllers\API\ActivityLogController;
use App\Http\Controllers\API\LoanPaymentController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\InvoiceReturnController;
use App\Http\Controllers\API\LoanAuthorityController;
use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\API\InvoicePaymentController;
use App\Http\Controllers\API\PurchaseReturnController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\API\EmpSalIncrementController;
use App\Http\Controllers\API\ProductCategoryController;
use App\Http\Controllers\API\PurchasePaymentController;
use App\Http\Controllers\API\TransferBalanceController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\TenantImpersonationController;
use App\Http\Controllers\API\NonInvoicePaymentController;
use App\Http\Controllers\API\NonPurchasePaymentController;
use App\Http\Controllers\API\InventoryAdjustmentController;
use App\Http\Controllers\API\SubscriptionInvoiceController;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\API\SubscriptionPaymentMethodController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

/*
 * Structured Routes
 */




Route::middleware([
    InitializeTenancyByDomainOrSubdomain::class,
    PreventAccessFromCentralDomains::class,
    'is_banned',
    'read_only',
])->group(function () {
    // API Routes
    // [GUEST API] Tenant Routes without protection
    Route::prefix('api')->group(function () {
        Route::post('login', [LoginController::class, 'login'])->name('tenant.login');
        Route::post('register', [RegisterController::class, 'register']);

        Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
        Route::post('password/reset', [ResetPasswordController::class, 'reset']);

        Route::post('oauth/{driver}', [OAuthController::class, 'redirect']);
        Route::get('oauth/{driver}/callback', [OAuthController::class, 'handleCallback'])->name('oauth.callback');

        Route::get('/impersonate/{token}', [TenantImpersonationController::class, 'impersonate']);
        Route::get('general-settings', [GeneralController::class, 'getGeneralSettings']);
    });

    // [PROTECTED API] Tenant Routes protected by Sanctum
    Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'api', 'as' => 'tenant.'], function () {
        Route::post('logout', [LoginController::class, 'logout']);
        // Dashboard stats
        Route::get('/dashboard-summery/{summeryType}', [DashboardController::class, 'dashboardSummery']);
        // Dashboard top-selling products
        Route::get('dashboard/top-selling-products', [DashboardController::class, 'topSellingProducts']);
        // Activities routes
        Route::get('dashboard/recent-invoices', [DashboardController::class, 'recentInvoices']);
        Route::get('dashboard/recent-purchases', [DashboardController::class, 'recentPurchases']);
        Route::get('dashboard/recent-expenses', [DashboardController::class, 'recentExpenses']);
        Route::get('dashboard/recent-transactions', [DashboardController::class, 'recentTransactions']);
        // Dashboard monthly sales and purchases
        Route::get('dashboard/monthly-payment-sent-received', [DashboardController::class, 'monthlyPaymentSentAndReceived']);
        // Dashboard top clients
        Route::get('dashboard/top-clients', [DashboardController::class, 'topClients']);
        // Dashboard stock alert
        Route::get('dashboard/stock-alert', [DashboardController::class, 'stockAlert']);
        // Dashboard monthly sales and purchases
        Route::get('dashboard/monthly-sales-purchases', [DashboardController::class, 'monthlySalesAndPurchases']);
        // Stock notification for navbar
        Route::get('dashboard/stock-notification', [DashboardController::class, 'stockNotification']);
        Route::get('stock-alert-products', [DashboardController::class, 'stockAlertProducts']);
        Route::get('stock-alert-products/search', [DashboardController::class, 'searchStockAlertProducts']);

        // get current user
        Route::get('user', [UserController::class, 'current']);
        Route::get('all-user', [UserController::class, 'allUser']);

        // general settings
        Route::post('update-settings', [GeneralController::class, 'updateGeneralSettings']);

        // Email routes
        Route::get('mail-configuration', [GeneralController::class, 'getSMTPforTenant']);
        Route::post('update-mail-configuration', [GeneralController::class, 'updateSMTPforTenant']);
        Route::get('/send-test-connection-email', [GeneralController::class, 'sendTestConnectionEmail']);

        // SMS routes
        Route::get('sms-configuration', [GeneralController::class, 'getSMSforTenant']);
        Route::post('update-sms-configuration', [GeneralController::class, 'updateSMSforTenant']);


        // Permission routes
        Route::get('/permissions/search', [PermissionController::class, 'search']);
        Route::get('/all-permissions', [PermissionController::class, 'allPermissions']);
        Route::apiResource('permissions', PermissionController::class);

        // Role routes
        Route::get('/roles/search', [RoleController::class, 'search']);
        Route::get('/all-roles', [RoleController::class, 'allRoles']);
        Route::apiResource('roles', RoleController::class);

        // Unit routes
        Route::get('/units/search', [UnitController::class, 'search']);
        Route::get('/all-units', [UnitController::class, 'allUnits']);
        Route::apiResource('units', UnitController::class);

        // Currency routes
        Route::get('/currencies/search', [CurrencyController::class, 'search']);
        Route::get('/all-currencies', [CurrencyController::class, 'allCurrencies']);
        Route::apiResource('currencies', CurrencyController::class);

        // VAT-RATE routes
        Route::get('/vat-rates/search', [VatRateController::class, 'search']);
        Route::get('/all-vat-rates', [VatRateController::class, 'allVatRates']);
        Route::get('/smtp-info', [GeneralController::class, 'getSMTPforTenant']);
        Route::apiResource('vat-rates', VatRateController::class);

        // Brand routes
        Route::get('/brands/search', [BrandController::class, 'search']);
        Route::get('/all-brands', [BrandController::class, 'allBrands']);
        Route::apiResource('brands', BrandController::class);

        // Payment method routes
        Route::get('/payment-methods/search', [PaymentMethodController::class, 'search']);
        Route::get('/all-payment-methods', [PaymentMethodController::class, 'allMethods']);
        Route::apiResource('payment-methods', PaymentMethodController::class);

        // Expense categories routes
        Route::get('/expense-categories/search', [ExpenseCatController::class, 'search']);
        Route::get('/all-expense-categories', [ExpenseCatController::class, 'allCategories']);
        Route::apiResource('expense-categories', ExpenseCatController::class);

        // Expense sub categories routes
        Route::get('/expense-sub-categories/search', [ExpSubCatController::class, 'search']);
        Route::get('/all-expense-sub-categories', [ExpSubCatController::class, 'allSubCategories']);
        Route::get('/sub-categories-by-category/{slug}', [ExpSubCatController::class, 'subCategoriesByCategory']);
        Route::apiResource('expense-sub-categories', ExpSubCatController::class);

        // Expense routes
        Route::get('/expenses/search/', [ExpenseController::class, 'search']);
        Route::apiResource('expenses', ExpenseController::class);

        // Purchase routes
        Route::get('/purchases/search', [PurchaseController::class, 'search']);
        Route::get('/due-purchases', [PurchaseController::class, 'duePurchases']);
        Route::post('/purchase/notify/{slug}', [PurchaseController::class, 'notifySupplier']);
        Route::post('/purchase-pay', [PurchaseController::class, 'storePurchasePayment']);
        Route::apiResource('purchases', PurchaseController::class);

        // Purchase return routes
        Route::post('/supplier/filter-purchases', [SupplierController::class, 'filterSupplierPurchases']);
        Route::get('/purchase-returns/search', [PurchaseReturnController::class, 'search']);
        Route::apiResource('purchase-returns', PurchaseReturnController::class);

        // Quotations routes
        Route::get('/quotations/search', [QuotationController::class, 'search']);
        Route::post('/quotation/notify/{slug}', [QuotationController::class, 'notifyCustomer']);
        Route::get('/all-quotations', [QuotationController::class, 'allQuotations']);
        Route::apiResource('quotations', QuotationController::class);

        // Invoice routes
        Route::get('/invoices/search', [InvoiceController::class, 'search']);
        Route::get('/due-invoices', [InvoiceController::class, 'allDueInvoices']);
        Route::post('/invoice/notify/{slug}', [InvoiceController::class, 'notifyCustomer']);
        Route::post('/invoices-pay', [InvoiceController::class, 'storeInvoicePayments']);
        Route::apiResource('invoices', InvoiceController::class);

        // Invoice return routes
        Route::get('/invoice-returns/search', [InvoiceReturnController::class, 'search']);
        Route::apiResource('invoice-returns', InvoiceReturnController::class);

        // Account routes
        Route::get('/accounts/search', [AccountController::class, 'search']);
        Route::get('/all-accounts', [AccountController::class, 'allAccounts']);
        Route::get('/accounts/transactions/{slug}', [AccountController::class, 'accountTransactions']);
        Route::get('/accounts/transactions/{slug}/search', [AccountController::class, 'searchTransactions']);
        Route::apiResource('accounts', AccountController::class);

        // Balance routes
        Route::get('/balances/search', [BalanceController::class, 'search']);
        Route::get('/all-balances', [BalanceController::class, 'allBalances']);
        Route::apiResource('balances', BalanceController::class);

        // Transfer balance routes
        Route::get('/balance-transfers/search', [TransferBalanceController::class, 'search']);
        Route::get('/all-balance-transfers', [TransferBalanceController::class, 'allBalances']);
        Route::apiResource('balance-transfers', TransferBalanceController::class);

        // Balance transfers routes
        Route::get('/transactions/search', [TransactionController::class, 'searchTransactions']);
        Route::get('/transactions', [TransactionController::class, 'allTransactions']);

        // Client invoice payment routes
        Route::get('/payments/invoice/search', [InvoicePaymentController::class, 'search']);
        Route::apiResource('/payments/invoice', InvoicePaymentController::class);

        // Client non invoice payment routes
        Route::get('/payments/non-invoice/search', [NonInvoicePaymentController::class, 'search']);
        Route::apiResource('/payments/non-invoice', NonInvoicePaymentController::class);

        // Supplier payment routes
        Route::get('/supplier/{slug}/purchases', [SupplierController::class, 'specificSupplierPurchases']);
        Route::get('/payments/purchase/search', [PurchasePaymentController::class, 'search']);
        Route::apiResource('payments/purchase', PurchasePaymentController::class);

        // Supplier non purchase payment routes
        Route::get('/suppliers-for-nonpurchase-payments', [SupplierController::class, 'suppliersForNonPurchasePayments']);
        Route::get('/payments/non-purchase/search', [NonPurchasePaymentController::class, 'search']);
        Route::apiResource('/payments/non-purchase', NonPurchasePaymentController::class);

        // Loan authorities routes
        Route::get('/loan-authorities/search', [LoanAuthorityController::class, 'search']);
        Route::get('/all-loan-authorities', [LoanAuthorityController::class, 'allAuthorities']);
        Route::apiResource('loan-authorities', LoanAuthorityController::class);

        // Loan routes
        Route::get('/loans/search', [LoanController::class, 'search']);
        Route::get('/all-loans', [LoanController::class, 'allLoans']);
        Route::apiResource('loans', LoanController::class);

        // Loan payment routes
        Route::get('/loan-payments/search', [LoanPaymentController::class, 'search']);
        Route::apiResource('loan-payments', LoanPaymentController::class);

        // Asset types routes
        Route::get('/asset-types/search', [AssetTypeController::class, 'search']);
        Route::get('/all-asset-types', [AssetTypeController::class, 'allAssets']);
        Route::apiResource('asset-types', AssetTypeController::class);

        // Asset routes
        Route::get('/assets/search', [AssetController::class, 'search']);
        Route::apiResource('assets', AssetController::class);

        // Employee payroll routes
        Route::get('/payroll/search', [PayrollController::class, 'search']);
        Route::get('/all-payroll', [PayrollController::class, 'allPayroll']);
        Route::apiResource('payroll', PayrollController::class);

        // Client routes
        Route::get('/clients/search', [ClientController::class, 'search']);
        Route::get('/all-clients', [ClientController::class, 'allClients']);
        Route::get('/clients-for-noninvoice-payments', [ClientController::class, 'clientsForNonInvoicePayments']);
        Route::get('/client/invoices/{slug}', [ClientController::class, 'clientInvoices']);
        Route::post('/client/filter-invoices', [ClientController::class, 'filterClientInvoices']);
        Route::get('/client/{slug}/invoices', [ClientController::class, 'specificClientInvoices']);
        Route::get('/client/{slug}/ledger', [ClientController::class, 'specificClientLedger']);
        Route::apiResource('clients', ClientController::class);
        // client import csv routes
        Route::post('/client-import', [ClientController::class, 'import']);

        // client invoice routes
        Route::get('/client/{slug}/all-invoices', [ClientController::class, 'clientAllInvoices']);
        Route::get('/client/{slug}/all-invoices/search', [ClientController::class, 'searchClientInvoices']);
        // client invoice return routes
        Route::get('/client/{slug}/invoice-returns', [ClientController::class, 'clientInvoiceReturns']);
        Route::get('/client/{slug}/invoice-returns/search', [ClientController::class, 'searchClientInvoiceReturns']);
        // client invoice payment routes
        Route::get('/client/{slug}/invoice-payments', [ClientController::class, 'clientInvoicePayments']);
        Route::get('/client/{slug}/invoice-payments/search', [ClientController::class, 'searchClientInvoicePayments']);
        // Client non invoice payment routes
        Route::get('/client/{slug}/non-invoice-payments', [ClientController::class, 'clientNonInvoicePayments']);
        Route::get('/client/{slug}/non-invoice-payments/search', [ClientController::class, 'searchClientNonInvoicePayments']);

        // Supplier routes
        Route::get('/suppliers/search', [SupplierController::class, 'search']);
        Route::get('/all-suppliers', [SupplierController::class, 'allSuppliers']);
        Route::get('/supplier/purchases/{slug}', [SupplierController::class, 'supplierPurchases']);
        Route::apiResource('suppliers', SupplierController::class);
        // client import csv routes
        Route::post('/supplier-import', [SupplierController::class, 'import']);

        // Supplier purchases
        Route::get('/purchases/supplier/{slug}', [SupplierController::class, 'purchasesBySupplier']);
        Route::get('/purchases/supplier/{slug}/search', [SupplierController::class, 'searchPurchasesBySupplier']);

        // Supplier purchase returns
        Route::get('/purchase-returns/supplier/{slug}', [SupplierController::class, 'purchaseReturnsBySupplier']);
        Route::get('/purchase-returns/supplier/{slug}/search', [SupplierController::class, 'searchPurchaseReturnsBySupplier']);

        // Supplier purchase payments
        Route::get('/payments/supplier/{slug}', [SupplierController::class, 'paymentsForSupplier']);
        Route::get('/payments/supplier/{slug}/search', [SupplierController::class, 'searchPaymentsForSupplier']);

        // Supplier non purchase transactions
        Route::get('/non-purchases/supplier/{slug}', [SupplierController::class, 'nonPurchaseTransForSupplier']);
        Route::get('/non-purchases/supplier/{slug}/search', [SupplierController::class, 'searchNonPurchaseTransForSupplier']);

        // Departments routes
        Route::get('/departments/search', [DepartmentController::class, 'search']);
        Route::get('/all-departments', [DepartmentController::class, 'allDepartments']);
        Route::apiResource('departments', DepartmentController::class);

        // Employee routes
        Route::get('/employees/search', [EmployeeController::class, 'search']);
        Route::get('/all-employees', [EmployeeController::class, 'allEmployees']);
        Route::apiResource('employees', EmployeeController::class);

        // Specific employee payroll
        Route::get('/employee-payroll/{slug}', [EmployeeController::class, 'employeePayroll']);
        Route::get('/employee-payroll/{slug}/search', [EmployeeController::class, 'searchEmployeePayroll']);

        // Specific employee salary increments
        Route::get('/employee-increments/{slug}', [EmployeeController::class, 'employeeIncrements']);
        Route::get('/employee-increments/{slug}/search', [EmployeeController::class, 'searchEmployeeIncrements']);

        // Employee salary increment routes
        Route::get('/increments/search', [EmpSalIncrementController::class, 'search']);
        Route::get('/all-increments', [EmpSalIncrementController::class, 'allIncrements']);
        Route::apiResource('increments', EmpSalIncrementController::class);

        // Product categories routes
        Route::get('/product-categories/search', [ProductCategoryController::class, 'search']);
        Route::get('/all-product-categories', [ProductCategoryController::class, 'allCategories']);
        Route::apiResource('product-categories', ProductCategoryController::class);

        // Product sub categories routes
        Route::get('/product-sub-categories/search', [ProSubCatController::class, 'search']);
        Route::get('/all-product-sub-categories', [ProSubCatController::class, 'allSubCategories']);
        Route::get('/pro-sub-categories-by-category/{slug}', [ProSubCatController::class, 'subCategoriesByCategory']);
        Route::get('/all-pro-sub-categories-by-category/{slug}', [ProSubCatController::class, 'allSubCategoriesByCategory']);
        Route::apiResource('product-sub-categories', ProSubCatController::class);


        // Product routes
        Route::get('/products/search', [ProductController::class, 'search']);
        Route::get('/products/search-from-pos', [ProductController::class, 'searchFromPos']);
        Route::get('/all-products-not-service', [ProductController::class, 'allProductsNotService']);
        Route::get('/all-products', [ProductController::class, 'allProducts']);
        Route::get('/all-products-paginated', [ProductController::class, 'allProductsPaginated']);
        Route::get('/all-products-for-select', [ProductController::class, 'allProductsForSelect']);
        Route::get('/generate-itemcode', [ProductController::class, 'generateItemCode']);
        Route::get('/products-by-sub-categories/{catSlug}/{subCatSlug}', [ProductController::class, 'productsBySubCategory']);
        Route::get('/all-products-by-sub-categories/{catSlug}/{subCatSlug}', [ProductController::class, 'allProductsBySubCategory']);
        Route::apiResource('products', ProductController::class);
        Route::post('/product-import', [ProductController::class, 'import']);


        // Inventory route
        Route::get('/inventory', [InventoryController::class, 'allInventory']);
        Route::get('/inventory/search', [InventoryController::class, 'searchInventory']);
        Route::get('/inventory-history/{slug}', [InventoryController::class, 'inventoryHistoryByItem']);

        // Inventory adjustment routes
        Route::get('/inventory-adjustments/search', [InventoryAdjustmentController::class, 'search']);
        Route::apiResource('/inventory-adjustments', InventoryAdjustmentController::class);

        // Report routes
        Route::get('/reports/balance-sheet', [ReportController::class, 'balanceSheet']);
        Route::post('/reports/summery', [ReportController::class, 'summeryReport']);
        Route::post('/reports/profit-loss', [ReportController::class, 'profitLossReport']);
        Route::post('/reports/expenses', [ReportController::class, 'expenseReport']);
        Route::post('/reports/items', [ReportController::class, 'itemsReport']);
        Route::post('/reports/inventory', [ReportController::class, 'inventoryReport']);
        Route::get('/reports/supplier-due-report', [ReportController::class, 'supplierDueReport']);
        Route::get('/reports/client-due-report', [ReportController::class, 'clientDueReport']);
        Route::post('/reports/sales-by-user-report', [ReportController::class, 'salesByUserReport']);
        Route::post('/reports/collection-by-user-report', [ReportController::class, 'collectionByUserReport']);
        Route::get('/reports/todayReport', [ReportController::class, 'todayReport']);

        // update profile
        Route::post('/update-profile', [DashboardController::class, 'updateProfile']);
        // database backup
        Route::post('/backup', [DashboardController::class, 'databaseBackup']);

        Route::get('/menu-search', [MenuController::class, 'searchMenu']);

        // Plans routes
        Route::get('/plans', [SubscriptionController::class, 'plans']);
        Route::get('/central-currency', [SubscriptionController::class, 'centralActiveCurrency']);
        Route::get('/central-plan-discount', [SubscriptionController::class, 'getCentralPlanDiscount']);
        Route::get('/central-currency-exchange-rate-info', [SubscriptionController::class, 'currencyExchangeRate']);

        Route::post('payments/download', [PaymentController::class, 'download']);
        Route::apiResource('payments', PaymentController::class)->only(['index']);


        Route::get('subscriptions/payment-methods', [SubscriptionPaymentMethodController::class, 'index']);

        Route::get('activity-log-specific', [ActivityLogController::class, 'specific']);
        Route::get('/activity-log', [ActivityLogController::class, 'index']);
        Route::get('/activity-log/search', [ActivityLogController::class, 'search']);

        // Subscriptions routes
        // Route::get('subscriptions/setup-intent', [SubscriptionController::class, 'getSetupIntent']);
        // Route::post('subscriptions/payment-methods', [SubscriptionController::class, 'postPaymentMethods']);
        // Route::delete('subscriptions/payment-methods/{paymentMethodId}', [SubscriptionController::class, 'removePaymentMethods']);
        // Route::get('subscriptions', [SubscriptionController::class, 'currentSubscription']);
        // Route::post('subscriptions', [SubscriptionController::class, 'createOrUpdateSubscription']);
        // Route::post('subscriptions/cancel', [SubscriptionController::class, 'cancelSubscription']);
        // Route::post('subscriptions/resume', [SubscriptionController::class, 'resumeSubscription']);

        // invoices routes
        Route::apiResource('subscription-invoices', SubscriptionInvoiceController::class)
            ->only(['index', 'store']);

        // Tenant details routes
        Route::get('/tenant/me', [TenantController::class, 'me']);

        // Domain routes
        Route::get('/domains', [DomainController::class, 'index']);
        Route::post('/domains', [DomainController::class, 'store']);
        Route::post('/domains/{domainId}', [DomainController::class, 'makePrimary']);
        Route::delete('/domains/{domainId}', [DomainController::class, 'delete']);

        // domain request
        Route::get('domain-requests', [App\Http\Controllers\API\DomainRequestController::class, 'index']);
        Route::post('domain-requests', [App\Http\Controllers\API\DomainRequestController::class, 'store']);
        Route::delete('domain-requests/{id}', [App\Http\Controllers\API\DomainRequestController::class, 'destroy']);
        Route::get('/server-ip', [ServerController::class, 'ip']);

        Route::apiResource('subscription-requests', App\Http\Controllers\API\SubscriptionRequestController::class)->only(['index', 'store']);

        // payment methods
        //Route::apiResource('payment-methods', \App\Http\Controllers\API\SubscriptionPaymentMethodController::class)->only('index', 'store');

        Route::get('server', [ServerController::class, 'runCommand']);


        Route::post('/rich-editor-file-upload', [RichEditorController::class, 'handleUpload']);


    });


    // email pdf generator routes
    Route::get('/invoice/pdf/{slug}', [PDFGeneratorController::class, 'generateInvoicePDF'])->name('email.invoice.pdf');
    Route::get('/purchase/pdf/{slug}', [PDFGeneratorController::class, 'generatePurchasePDF'])->name('email.purchase.pdf');
    Route::get('/quotation/pdf/{slug}', [PDFGeneratorController::class, 'generateQuotationPDF'])->name('email.quotation.pdf');


    // pdf download blade routes
    Route::get('/setup/brands/pdf', [TableExportController::class, 'brandsPDF'])->name('brands.pdf');
    Route::get('/setup/currencies/pdf', [TableExportController::class, 'currenciesPDF'])->name('currencies.pdf');
    Route::get('/setup/units/pdf', [TableExportController::class, 'unitsPDF'])->name('units.pdf');
    Route::get('/setup/vat-rates/pdf', [TableExportController::class, 'vatRatesPDF'])->name('vatRates.pdf');
    Route::get('/setup/roles/pdf', [TableExportController::class, 'rolesPDF'])->name('roles.pdf');
    Route::get('/setup/payment-methods/pdf', [TableExportController::class, 'paymentMethodsPDF'])->name('paymentMethods.pdf');

    Route::get('/expense-categories/pdf', [TableExportController::class, 'expCategoriesPDF'])->name('expCategories.pdf');
    Route::get('/expense-categories/export/excel', [TableExportController::class, 'expCategoriesExportExcel'])->name('expCategories.export.excel');
    Route::get('/expense-sub-categories/pdf', [TableExportController::class, 'expSubCategoriesPDF'])->name('expSubCategories.pdf');
    Route::get('/expense-sub-categories/export/excel', [TableExportController::class, 'expSubCategoriesExportExcel'])->name('expSubCategories.export.excel');
    Route::get('/expenses/pdf', [TableExportController::class, 'expensesPDF'])->name('expenses.pdf');
    Route::get('/expenses/export/excel', [TableExportController::class, 'expensesExportExcel'])->name('expenses.export.excel');

    Route::get('/purchases/pdf', [TableExportController::class, 'purchasesPDF'])->name('purchases.pdf');
    Route::get('/purchases/export/excel', [TableExportController::class, 'purchasesExportExcel'])->name('purchases.export.excel');
    Route::get('/purchase-returns/pdf', [TableExportController::class, 'purchaseReturnsPDF'])->name('purchaseReturns.pdf');
    Route::get('/purchase-returns/export/excel', [TableExportController::class, 'purchaseReturnsExportExcel'])->name('purchaseReturns.export.excel');

    Route::get('/quotations/pdf', [TableExportController::class, 'quotationsPDF'])->name('quotations.pdf');
    Route::get('/quotations/export/excel', [TableExportController::class, 'quotationsExportExcel'])->name('quotations.export.excel');
    Route::get('/invoices/pdf', [TableExportController::class, 'invoicePDF'])->name('invoices.pdf');
    Route::get('/invoices/export/export', [TableExportController::class, 'invoiceExportExcel'])->name('invoices.export.export');
    Route::get('/invoice-returns/pdf', [TableExportController::class, 'invoiceReturnPDF'])->name('invoiceReturns.pdf');
    Route::get('/invoice-returns/export/excel', [TableExportController::class, 'invoiceReturnExportExcel'])->name('invoiceReturns.export.excel');

    Route::get('/accounts/pdf', [TableExportController::class, 'accountsPDF'])->name('accounts.pdf');
    Route::get('//accounts/export/excel', [TableExportController::class, 'accountsExportExcel'])->name('accounts.export.excel');
    Route::get('/account-transactions/pdf/{slug}', [TableExportController::class, 'accountTransactionsPDF'])->name('account.transactions.pdf');
    Route::get('/cashbook/balance-adjustments/pdf', [TableExportController::class, 'nonInvoiceBalancesPDF'])->name('account.balances.pdf');
    Route::get('/cashbook/balance-adjustments/export/excel', [TableExportController::class, 'nonInvoiceBalancesExportExcel'])->name('account.balances.export.excel');
    Route::get('/cashbook/transfer-balances/pdf', [TableExportController::class, 'transferBalancesPDF'])->name('account.transferBalances.pdf');
    Route::get('/cashbook/transfer-balances/export/excel', [TableExportController::class, 'transferBalancesExportExcel'])->name('account.transferBalances.export.excel');
    Route::get('/cashbook/transactions/pdf', [TableExportController::class, 'transactionsPDF'])->name('transactions.pdf');
    Route::get('/cashbook/transactions/export/excel', [TableExportController::class, 'transactionsExportExcel'])->name('transactions.export.excel');

    Route::get('/payments/clients/non-invoice/pdf', [TableExportController::class, 'nonInvoicePaymentsPDF'])->name('nonInvoicePayments.pdf');
    Route::get('/payments/clients/non-invoice/export/excel', [TableExportController::class, 'nonInvoicePaymentsExportExcel'])->name('nonInvoicePayments.export.excel');
    Route::get('/payments/clients/invoice/pdf', [TableExportController::class, 'invoicePaymentsPDF'])->name('invoicePayments.pdf');
    Route::get('/payments/clients/invoice/export/excel', [TableExportController::class, 'invoicePaymentsExportExcel'])->name('invoicePayments.export.excel');
    Route::get('/payments/suppliers/non-purchase/pdf', [TableExportController::class, 'nonPurchasePaymentsPDF'])->name('nonPurchasePayments.pdf');
    Route::get('/payments/suppliers/non-purchase/export/excel', [TableExportController::class, 'nonPurchasePaymentsExportExcel'])->name('nonPurchasePayments.export.excel');
    Route::get('/payments/suppliers/purchase/pdf', [TableExportController::class, 'purchasePaymentsPDF'])->name('locSupplierPayments.pdf');
    Route::get('/payments/suppliers/purchase/export/excel', [TableExportController::class, 'purchasePaymentsExportExcel'])->name('locSupplierPayments.export.excel');

    Route::get('/loan-authorities/pdf', [TableExportController::class, 'loanAuthoritiesPDF'])->name('loanAuthorities.pdf');
    Route::get('/loan-authorities/export/excel', [TableExportController::class, 'loanAuthoritiesExportExcel'])->name('loanAuthorities.export.excel');
    Route::get('/loans/pdf', [TableExportController::class, 'loansPDF'])->name('loans.pdf');
    Route::get('/loans/export/excel', [TableExportController::class, 'loansExportExcel'])->name('loans.export.excel');
    Route::get('/loan-payments/pdf', [TableExportController::class, 'loanPaymentsPDF'])->name('loanPayments.pdf');
    Route::get('/loan-payments/export/excel', [TableExportController::class, 'loanPaymentsExportExcel'])->name('loanPayments.export.excel');

    Route::get('/asset-types/pdf', [TableExportController::class, 'assetTypesPDF'])->name('assetTypes.pdf');
    Route::get('/asset-types/export/excel', [TableExportController::class, 'assetTypesExportExcel'])->name('assetTypes.export.excel');
    Route::get('/assets/pdf', [TableExportController::class, 'assetsPDF'])->name('assets.pdf');
    Route::get('/assets/export/excel', [TableExportController::class, 'assetsExportExcel'])->name('assets.export.excel');

    Route::get('/payroll/pdf', [TableExportController::class, 'payrollPDF'])->name('payroll.pdf');
    Route::get('/payroll/export/excel', [TableExportController::class, 'payrollExportExcel'])->name('payroll.export.excel');

    Route::get('/clients/pdf', [TableExportController::class, 'clientsPDF'])->name('clients.pdf');
    Route::get('/clients/export/excel', [TableExportController::class, 'clientsExportExcel'])->name('clients.export.excel');
    Route::get('/suppliers/pdf', [TableExportController::class, 'suppliersPDF'])->name('suppliers.pdf');
    Route::get('/suppliers/export/excel', [TableExportController::class, 'suppliersExportExcel'])->name('suppliers.export.excel');

    Route::get('/departments/pdf', [TableExportController::class, 'departmentsPDF'])->name('departments.pdf');
    Route::get('/departments/export/excel', [TableExportController::class, 'departmentsExportExcel'])->name('departments.export.excel');
    Route::get('/employees/pdf', [TableExportController::class, 'employeesPDF'])->name('employees.pdf');
    Route::get('/employee/export/excel', [TableExportController::class, 'employeesExportExcel'])->name('employees.export.excel');
    Route::get('/increments/pdf', [TableExportController::class, 'incrementsPDF'])->name('increments.pdf');
    Route::get('/increments/export/excel', [TableExportController::class, 'incrementsExportExcel'])->name('increments.export.excel');

    Route::get('/product-categories/pdf', [TableExportController::class, 'productCategoriesPDF'])->name('productCategories.pdf');
    Route::get('/product-categories/export/excel', [TableExportController::class, 'productCategoriesExportExcel'])->name('productCategories.export.excel');
    Route::get('/product-sub-categories/pdf', [TableExportController::class, 'productSubCategoriesPDF'])->name('productSubCategories.pdf');
    Route::get('/product-sub-categories/export/excel', [TableExportController::class, 'productSubCategoriesExportExcel'])->name('productSubCategories.export.excel');
    Route::get('/products/pdf', [TableExportController::class, 'productsPDF'])->name('products.pdf');
    Route::get('/products/export/excel', [TableExportController::class, 'productsExportExcel'])->name('products.export.excel');

    Route::get('/inventory-adjustments/pdf', [TableExportController::class, 'inventoryAdjustmentsPDF'])->name('inventoryAdjustments.pdf');
    Route::get('/inventory-adjustments/excel', [TableExportController::class, 'inventoryAdjustmentsExcel'])->name('inventoryAdjustments.excel');
    Route::get('/inventory/excel', [TableExportController::class, 'inventoryExcel'])->name('inventory.excel');

    Route::get('/supplier-payable-report/export/excel', [TableExportController::class, 'supplierPayableReportExportExcel'])->name('supplierPayableReport.export.excel');
    Route::get('/client-receivable-report/export/excel', [TableExportController::class, 'clientReceivableReportExportExcel'])->name('clientReceivableReport.export.excel');
    Route::get('/sales-by-user-report/export/excel', [TableExportController::class, 'salesByUserReportExportExcel'])->name('salesByUserReport.export.excel');
    Route::get('/collection-by-user-report/export/excel', [TableExportController::class, 'collectionByUserReportExportExcel'])->name('collectionByUserReport.export.excel');

    // product templates
    Route::get('/product-import-template', [ProductController::class, 'importTemplate']);

    // Tenant SPA routes
    Route::get('{path}', SpaController::class)->where('path', '^(?!.*api).*$');
});