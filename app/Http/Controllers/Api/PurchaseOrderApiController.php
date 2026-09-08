<?php

namespace App\Http\Controllers\Api;

use App\Enums\PurchaseOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrder\StorePurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\UpdatePurchaseOrderStatusRequest;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class PurchaseOrderApiController extends Controller
{
    public function __construct(
        protected PurchaseOrderService $purchaseOrderService
    ) {}

    /**
     * Create a new purchase order.
     */
    public function store(StorePurchaseOrderRequest $request): JsonResponse
    {
        $purchaseOrder = $this->purchaseOrderService->createPurchaseOrder($request->validated());

        return response()->json([
            'message' => 'Purchase order created successfully.',
            'data' => $purchaseOrder,
        ], 201);
    }

    /**
     * Update purchase order status.
     */
    public function updateStatus(
        UpdatePurchaseOrderStatusRequest $request,
        PurchaseOrder $purchaseOrder
    ): JsonResponse {
        $targetStatus = PurchaseOrderStatus::from($request->validated('status'));

        try {
            $updatedOrder = $this->purchaseOrderService->transitionStatus(
                $purchaseOrder,
                $targetStatus
            );

            return response()->json([
                'message' => 'Purchase order status updated successfully.',
                'data' => $updatedOrder,
            ], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => [
                    'status' => [$e->getMessage()],
                ],
            ], 422);
        }
    }
}
