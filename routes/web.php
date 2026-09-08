<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])
    ->name('purchase-orders.create');

Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])
    ->name('purchase-orders.store');

Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])
    ->name('purchase-orders.show');

Route::patch('/purchase-orders/{purchaseOrder}/status', [PurchaseOrderController::class, 'updateStatus'])
    ->name('purchase-orders.status');

Route::get('/inventory', [InventoryController::class, 'index'])
    ->name('inventory.index');