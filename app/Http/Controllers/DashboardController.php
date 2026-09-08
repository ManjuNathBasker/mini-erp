<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderStatus;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function index(): View
    {
        $activeProductsCount = Product::active()->count();

        $lowStockCount = Product::active()->lowStock()->count();

        $receivedExpenditure = PurchaseOrder::where('status', PurchaseOrderStatus::RECEIVED->value)
            ->sum('total_amount');

        $latestPurchaseOrders = PurchaseOrder::with('supplier')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'activeProductsCount',
            'lowStockCount',
            'receivedExpenditure',
            'latestPurchaseOrders'
        ));
    }
}
