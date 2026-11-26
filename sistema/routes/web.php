<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('template/administrador');
// });

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::resource('customers', CustomerController::class);
    Route::resource('sales', SaleController::class);

    Route::middleware('role:Administrador,Químico farmacéutico')->group(function () {
        Route::resource('medicines', MedicineController::class)->except('show');
        Route::resource('suppliers', SupplierController::class)->except('show');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales/pdf', [ReportController::class, 'salesPdf'])->name('reports.sales.pdf');
        Route::get('/reports/inventory/pdf', [ReportController::class, 'inventoryPdf'])->name('reports.inventory.pdf');
        Route::get('/reports/low-stock/pdf', [ReportController::class, 'lowStockPdf'])->name('reports.low-stock.pdf');
        Route::get('/reports/expiry/pdf', [ReportController::class, 'expiryPdf'])->name('reports.expiry.pdf');
        Route::get('/reports/customers/pdf', [ReportController::class, 'customersPdf'])->name('reports.customers.pdf');
        Route::get('/reports/users/pdf', [ReportController::class, 'usersPdf'])->name('reports.users.pdf');

        Route::get('/reports/excel', [ExcelController::class, 'index'])->name('reports.excel');
        Route::post('/reports/medicines/import', [ExcelController::class, 'medicines'])->name('reports.medicines.import');
        Route::get('/reports/medicines/export', [ExcelController::class, 'exportMedicines'])->name('reports.medicines.export');
        Route::get('/reports/customers/export', [ExcelController::class, 'exportCustomers'])->name('reports.customers.export');
        Route::get('/reports/sales/export', [ExcelController::class, 'exportSales'])->name('reports.sales.export');
    });

    Route::middleware('role:Administrador')->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('roles', RoleController::class)->except('show');
        Route::resource('users', UserController::class)->except('show');
    });
});
