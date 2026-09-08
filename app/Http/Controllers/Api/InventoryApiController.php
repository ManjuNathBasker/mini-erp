<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class InventoryApiController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::active()
            ->select([
                'id',
                'sku',
                'name',
                'unit_price',
                'stock_quantity',
                'low_stock_threshold',
            ])
            ->paginate(15);

        return response()->json([
            'message' => 'Inventory retrieved successfully.',
            'data' => $products,
        ]);
    }

    public function lowStock(): JsonResponse
    {
        $products = Product::active()
            ->lowStock()
            ->select([
                'id',
                'sku',
                'name',
                'unit_price',
                'stock_quantity',
                'low_stock_threshold',
            ])
            ->paginate(15);

        return response()->json([
            'message' => 'Low stock inventory retrieved successfully.',
            'data' => $products,
        ]);
    }
}