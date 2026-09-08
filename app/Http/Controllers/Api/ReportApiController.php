<?php

namespace App\Http\Controllers\Api;

use App\Enums\PurchaseOrderStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportApiController extends Controller
{
    /**
     * Get supplier spending report for RECEIVED purchase orders.
     */
    public function supplierSpend(): JsonResponse
    {
        $supplierSpend = DB::table('purchase_orders')
            ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->where('purchase_orders.status', PurchaseOrderStatus::RECEIVED->value)
            ->select(
                'suppliers.id as supplier_id',
                'suppliers.name as supplier_name',
                DB::raw('SUM(purchase_orders.total_amount) as total_spend')
            )
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderBy('total_spend', 'desc')
            ->get();

        return response()->json([
            'message' => 'Supplier spend retrieved successfully.',
            'data' => $supplierSpend,
        ], 200);
    }
}
