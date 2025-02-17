<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DemoTenantMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = [
            [
                'name' => 'Dashboard',
                'route' => 'home',
                'text' => 'Dashboard',
                'icon' => 'nav-icon fas fa-home',
                'status' => true,
            ],
            [
                'name' => 'Categories',
                'route' => 'expenseCats.index',
                'text' => 'Categories',
                'icon' => 'fas fa-tags nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Sub Categories',
                'route' => 'expenseSubCats.index',
                'text' => 'Sub Categories',
                'icon' => 'fas fa-code-branch nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Expenses List',
                'route' => 'expenses.index',
                'text' => 'Expenses List',
                'icon' => 'fas fa-list-ul nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Purchases List',
                'route' => 'purchases.index',
                'text' => 'Purchases List',
                'icon' => 'fas fa-truck-loading nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Return List',
                'route' => 'purchaseReturns.index',
                'text' => 'Returns List',
                'icon' => 'fas fa-undo-alt nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Quotations List',
                'route' => 'quotations.index',
                'text' => 'Quotations List',
                'icon' => 'fas fa-th-list nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Invoices List',
                'route' => 'invoices.index',
                'text' => 'Invoices List',
                'icon' => 'fas fa-file-invoice nav-icon',
                'status' => true,
            ],
            [
                'name' => 'POS',
                'route' => 'pos.create',
                'text' => 'POS',
                'icon' => 'fas fa-cash-register nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Returns List',
                'route' => 'invoiceReturns.index',
                'text' => 'Returns List',
                'icon' => 'fas fa-undo-alt nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Accounts',
                'route' => 'accounts.index',
                'text' => 'Accounts',
                'icon' => 'fas fa-grip-horizontal nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Balance Adjustments',
                'route' => 'balances.index',
                'text' => 'Balance Adjustments',
                'icon' => 'fas fa-sliders-h nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Balance Transfers',
                'route' => 'transferBalances.index',
                'text' => 'Balance Transfers',
                'icon' => 'fas fa-exchange-alt nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Transaction History',
                'route' => 'transactions.index',
                'text' => 'Transaction History',
                'icon' => 'fas fa-history nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Invoice',
                'route' => 'invoicePayments.index',
                'text' => 'Invoice',
                'icon' => 'fas fa-file-invoice nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Non Invoice',
                'route' => 'nonInvoicePayments.index',
                'text' => 'Non Invoice',
                'icon' => 'fas fa-file-alt nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Purchase',
                'route' => 'purchasePayments.index',
                'text' => 'Purchase',
                'icon' => 'fas fa-plane-departure nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Non Purchase',
                'route' => 'nonPurchasePayments.index',
                'text' => 'Non Purchase',
                'icon' => 'fas fa-truck-pickup nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Authorities',
                'route' => 'authorities.index',
                'text' => 'Authorities',
                'icon' => 'fas fa-building nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Loans',
                'route' => 'loans.index',
                'text' => 'Loans',
                'icon' => 'fas fa-list-ul nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Payments',
                'route' => 'loanPayments.index',
                'text' => 'Payments',
                'icon' => 'fas fa-receipt nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Types',
                'route' => 'assetTypes.index',
                'text' => 'Types',
                'icon' => 'fas fa-tags nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Assets',
                'route' => 'assets.index',
                'text' => 'Assets',
                'icon' => 'fas fa-list-ul nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Payroll',
                'route' => 'payroll.index',
                'text' => 'Payroll',
                'icon' => 'nav-icon fas fa-clipboard-list',
                'status' => true,
            ],
            [
                'name' => 'Clients',
                'route' => 'clients.index',
                'text' => 'Clients',
                'icon' => 'nav-icon fas fa-users',
                'status' => true,
            ],
            [
                'name' => 'Suppliers',
                'route' => 'suppliers.index',
                'text' => 'Suppliers',
                'icon' => 'nav-icon fas fa-people-carry',
                'status' => true,
            ],
            [
                'name' => 'Departments',
                'route' => 'departments.index',
                'text' => 'Departments',
                'icon' => 'fas fa-server nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Employees List',
                'route' => 'employees.index',
                'text' => 'Employees List',
                'icon' => 'fas fa-list-ul nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Increments',
                'route' => 'Increments',
                'text' => 'Employees List',
                'icon' => 'fas fa-list-ul nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Categories',
                'route' => 'productCats.index',
                'text' => 'Categories',
                'icon' => 'fas fa-tags nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Sub Categories',
                'route' => 'productSubCats.index',
                'text' => 'Sub Categories',
                'icon' => 'fas fa-code-branch nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Products List',
                'route' => 'products.index',
                'text' => 'Products List',
                'icon' => 'fas fa-list-ul nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Barcode',
                'route' => 'Barcode',
                'text' => 'Products List',
                'icon' => 'fas fa-barcode nav-icon',
                'status' => true,
            ],
            [
                'name' => 'View Inventory',
                'route' => 'inventory.index',
                'text' => 'View Inventory',
                'icon' => 'fas fa-pallet nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Inventory Adjustment',
                'route' => 'adjustments.index',
                'text' => 'Inventory Adjustment',
                'icon' => 'fas fa-sliders-h nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Balance Sheet',
                'route' => 'reports.balanceSheet',
                'text' => 'Balance Sheet',
                'icon' => 'fas fa-file-invoice-dollar nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Today Report',
                'route' => 'reports.todayReport',
                'text' => 'Today Report',
                'icon' => 'fas fa-file-invoice-dollar nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Summary Report',
                'route' => 'reports.summary',
                'text' => 'Summary Report',
                'icon' => 'fas fa-file-contract nav-icon',
                'status' => true,
            ],
            [
                'name' => 'profit/Loss Report',
                'route' => 'reports.profitLoss',
                'text' => 'Profit/Loss Report',
                'icon' => 'fas fa-chart-line nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Expenses Report',
                'route' => 'reports.expenses',
                'text' => 'Expense Report',
                'icon' => 'fas fa-chart-pie nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Item Report',
                'route' => 'reports.items',
                'text' => 'Item Report',
                'icon' => 'fas fa-chart-bar nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Inventory Report',
                'route' => 'reports.inventory',
                'text' => 'Inventory Report',
                'icon' => 'fas fa-chart-pie nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Supplier Payable Report',
                'route' => 'reports.supplierPayableReport',
                'text' => 'Supplier Payable Report',
                'icon' => 'fas fa-file-invoice-dollar nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Client Receivable Report',
                'route' => 'reports.clientReceivableReport',
                'text' => 'Client Receivable Report',
                'icon' => 'fas fa-file-invoice-dollar nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Sales By User Report',
                'route' => 'reports.salesByUserReport',
                'text' => 'Sales By User Report',
                'icon' => 'fas fa-file-invoice-dollar nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Collection By User Report',
                'route' => 'reports.collectionByUserReport',
                'text' => 'Collection By User Report',
                'icon' => 'fas fa-file-invoice-dollar nav-icon',
                'status' => true,
            ],
            [
                'name' => 'Setup',
                'route' => 'setup.general',
                'text' => 'Setup',
                'icon' => 'nav-icon fas fa-cogs',
                'status' => true,
            ],
            [
                'name' => 'Profile',
                'route' => 'profile',
                'text' => 'Profile',
                'icon' => 'nav-icon fas fa-user-circle',
                'status' => true,
            ],
            [
                'name' => 'Activity log',
                'route' => 'activity.log',
                'text' => 'Activity log',
                'icon' => 'nav-icon fa fa-bell',
                'status' => true,
            ]
        ];

        // insert if not exist
        foreach ($menus as $menu) {
            DB::table('menus')->updateOrInsert(
                ['route' => $menu['route']],
                $menu
            );
        }
    }
}
