<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderStatus;
use App\Http\Requests\PurchaseOrder\StorePurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\UpdatePurchaseOrderStatusRequest;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchaseOrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;

class PurchaseOrderController extends Controller
{
    public function __construct(
        protected PurchaseOrderService $purchaseOrderService
    ) {}

    /**
     * Show the form for creating a new purchase order.
     */
    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();

        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created purchase order in storage.
     */
    public function store(StorePurchaseOrderRequest $request): RedirectResponse
    {
        $purchaseOrder = $this->purchaseOrderService->createPurchaseOrder($request->validated());

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase order created successfully.');
    }

    /**
     * Display the specified purchase order.
     */
    public function show(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load(['supplier', 'items.product']);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Update the status of the specified purchase order.
     */
    public function updateStatus(
        UpdatePurchaseOrderStatusRequest $request,
        PurchaseOrder $purchaseOrder
    ): RedirectResponse {
        try {
            $targetStatus = PurchaseOrderStatus::from($request->validated('status'));

            $this->purchaseOrderService->transitionStatus($purchaseOrder, $targetStatus);

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase order status updated successfully.');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
