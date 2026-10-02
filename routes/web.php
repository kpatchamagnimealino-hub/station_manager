<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\StationStockController;
use App\Http\Controllers\SupplyController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('dashboard');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', EnsureUserIsAdmin::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
});

Route::middleware(['auth', 'role:station_manager'])->group(function () {
    Route::get('/responsable', [AdminController::class, 'index'])->name('manager.dashboard');
});

Route::middleware(['auth', 'role:general_director'])->group(function () {
    Route::get('/direction', [AdminController::class, 'index'])->name('director.dashboard');
});

Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/admin/stocks', [StationStockController::class, 'index'])->name('admin.stocks.index');
    Route::post('/admin/stocks', [StationStockController::class, 'store'])->name('admin.stocks.store');

    Route::get('/admin/stations', [StationController::class, 'index'])->name('admin.stations.index');
    Route::get('/admin/stations/create', [StationController::class, 'create'])->name('admin.stations.create');
    Route::post('/admin/stations', [StationController::class, 'store'])->name('admin.stations.store');
    Route::put('/admin/stations/{station}', [StationController::class, 'update'])->name('admin.stations.update');

    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
});

Route::middleware(['auth', 'role:administrator,station_manager'])->group(function () {
    Route::get('/admin/supplies', [SupplyController::class, 'index'])->name('admin.supplies.index');
    Route::get('/admin/supplies/create', [SupplyController::class, 'create'])->name('admin.supplies.create');
    Route::post('/admin/supplies', [SupplyController::class, 'store'])->name('admin.supplies.store');

    Route::get('/admin/sales', [SaleController::class, 'index'])->name('admin.sales.index');
    Route::get('/admin/sales/create', [SaleController::class, 'create'])->name('admin.sales.create');
    Route::post('/admin/sales', [SaleController::class, 'store'])->name('admin.sales.store');
});

Route::middleware(['auth', 'role:administrator,general_director'])->group(function () {
    Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports.index');
    Route::get('/admin/reports/export-pdf', [AdminController::class, 'exportPdf'])->name('admin.reports.pdf');
    Route::get('/admin/reports/export-excel', [AdminController::class, 'exportExcel'])->name('admin.reports.excel');
    Route::get('/admin/reports/daily/{date}/{format}', [AdminController::class, 'downloadDailyReport'])
        ->where(['date' => '\\d{4}-\\d{2}-\\d{2}', 'format' => 'csv|pdf'])
        ->name('admin.reports.daily');
});
