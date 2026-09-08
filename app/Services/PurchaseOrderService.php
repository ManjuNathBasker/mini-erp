<?php

namespace App\Services;

use App\Enums\PurchaseOrderStatus;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PurchaseOrderService
{
    /**
     * Create a new Purchase Order in DRAFT status with line items.
     *
     * @param  array  $data
     * @return PurchaseOrder
     */
    public function createPurchaseOrder(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            do {
                $poNumber = 'PO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            } while (PurchaseOrder::where('po_number', $poNumber)->exists());

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $poNumber,
                'supplier_id' => $data['supplier_id'],
                'status' => PurchaseOrderStatus::DRAFT,
                'order_date' => $data['order_date'],
                'total_amount' => 0,
                'notes' => $data['notes'] ?? null,
            ]);

            $totalAmount = 0;

            foreach ($data['items'] as $itemData) {
                $quantity = (int) $itemData['quantity'];
                $unitPrice = (float) $itemData['unit_price'];

                if ($quantity <= 0) {
                    throw new InvalidArgumentException("Item quantity must be greater than zero.");
                }

                $subtotal = round($quantity * $unitPrice, 2);

                $purchaseOrder->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            $purchaseOrder->update([
                'total_amount' => round($totalAmount, 2),
            ]);

            return $purchaseOrder->load(['supplier', 'items.product']);
        });
    }

    /**
     * Transition a Purchase Order to a new status.
     *
     * @param  PurchaseOrder  $purchaseOrder
     * @param  PurchaseOrderStatus  $newStatus
     * @return PurchaseOrder
     */
    public function transitionStatus(
        PurchaseOrder $purchaseOrder,
        PurchaseOrderStatus $newStatus
    ): PurchaseOrder {
        return DB::transaction(function () use ($purchaseOrder, $newStatus) {
            // 1. Lock the PO row BEFORE checking current status to prevent race conditions
            $lockedPo = PurchaseOrder::where('id', $purchaseOrder->id)
                ->lockForUpdate()
                ->firstOrFail();

            // 2. Validate state transition using Enum rule
            if (! $lockedPo->status->canTransitionTo($newStatus)) {
                throw new InvalidArgumentException(
                    "Cannot transition purchase order {$lockedPo->po_number} from status {$lockedPo->status->value} to {$newStatus->value}."
                );
            }

            // 3. Stock update is performed ONLY when transitioning to RECEIVED
            if ($newStatus === PurchaseOrderStatus::RECEIVED) {
                $items = $lockedPo->items()->get();

                // Sort product IDs to prevent database lock wait deadlocks
                $productIds = $items->pluck('product_id')->unique()->sort()->values();

                // Lock affected product rows in deterministic order
                $products = Product::whereIn('id', $productIds)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($items as $item) {
                    $quantity = (int) $item->quantity;

                    if ($quantity <= 0) {
                        throw new InvalidArgumentException(
                            "Invalid stock increment quantity ({$quantity}) for product ID {$item->product_id}."
                        );
                    }

                    $product = $products->get($item->product_id);

                    if (! $product) {
                        throw new InvalidArgumentException(
                            "Product ID {$item->product_id} referenced in PO line items was not found."
                        );
                    }

                    $product->increment('stock_quantity', $quantity);
                }
            }

            // 4. Update the Purchase Order status
            $lockedPo->update([
                'status' => $newStatus,
            ]);

            // 5. Return fresh model with relationships loaded
            return $lockedPo->fresh(['supplier', 'items.product']);
        });
    }
}
