<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InventoryApiController;
use App\Http\Controllers\Api\PurchaseOrderApiController;
use App\Http\Controllers\Api\ReportApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/inventory', [InventoryApiController::class, 'index']);
    Route::get('/inventory/low-stock', [InventoryApiController::class, 'lowStock']);

    Route::post('/purchase-orders', [PurchaseOrderApiController::class, 'store']);
    Route::patch('/purchase-orders/{purchaseOrder}/status', [PurchaseOrderApiController::class, 'updateStatus']);

    Route::get('/reports/supplier-spend', [ReportApiController::class, 'supplierSpend']);
});
