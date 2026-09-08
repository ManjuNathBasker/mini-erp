<?php

namespace Tests\Feature;

use App\Enums\PurchaseOrderStatus;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_receiving_purchase_order_increases_product_stock(): void
    {
        $supplier = Supplier::create([
            'name' => 'Test Supplier',
        ]);

        $product = Product::create([
            'sku' => 'TEST-001',
            'name' => 'Test Product',
            'unit_price' => 100.00,
            'stock_quantity' => 10,
            'low_stock_threshold' => 5,
            'is_active' => true,
        ]);

        $purchaseOrder = app(PurchaseOrderService::class)->createPurchaseOrder([
            'supplier_id' => $supplier->id,
            'order_date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100.00,
                ],
            ],
        ]);

        app(PurchaseOrderService::class)->transitionStatus(
            $purchaseOrder,
            PurchaseOrderStatus::APPROVED
        );

        app(PurchaseOrderService::class)->transitionStatus(
            $purchaseOrder,
            PurchaseOrderStatus::RECEIVED
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 15,
        ]);

        $this->assertDatabaseHas('purchase_orders', [
            'id' => $purchaseOrder->id,
            'status' => PurchaseOrderStatus::RECEIVED->value,
        ]);
    }

    public function test_received_purchase_order_cannot_be_modified(): void
    {
        $supplier = Supplier::create([
            'name' => 'Test Supplier',
        ]);

        $product = Product::create([
            'sku' => 'TEST-002',
            'name' => 'Test Product',
            'unit_price' => 100.00,
            'stock_quantity' => 10,
            'low_stock_threshold' => 5,
            'is_active' => true,
        ]);

        $service = app(PurchaseOrderService::class);

        $purchaseOrder = $service->createPurchaseOrder([
            'supplier_id' => $supplier->id,
            'order_date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100.00,
                ],
            ],
        ]);

        $service->transitionStatus(
            $purchaseOrder,
            PurchaseOrderStatus::APPROVED
        );

        $service->transitionStatus(
            $purchaseOrder,
            PurchaseOrderStatus::RECEIVED
        );

        $this->expectException(\InvalidArgumentException::class);

        $service->transitionStatus(
            $purchaseOrder,
            PurchaseOrderStatus::CANCELLED
        );
    }

    public function test_cancelled_purchase_order_cannot_be_modified(): void
    {
        $supplier = Supplier::create([
            'name' => 'Test Supplier',
        ]);

        $product = Product::create([
            'sku' => 'TEST-003',
            'name' => 'Test Product',
            'unit_price' => 100.00,
            'stock_quantity' => 10,
            'low_stock_threshold' => 5,
            'is_active' => true,
        ]);

        $service = app(PurchaseOrderService::class);

        $purchaseOrder = $service->createPurchaseOrder([
            'supplier_id' => $supplier->id,
            'order_date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 100.00,
                ],
            ],
        ]);

        $service->transitionStatus(
            $purchaseOrder,
            PurchaseOrderStatus::CANCELLED
        );

        $this->expectException(\InvalidArgumentException::class);

        $service->transitionStatus(
            $purchaseOrder,
            PurchaseOrderStatus::APPROVED
        );
    }
}
