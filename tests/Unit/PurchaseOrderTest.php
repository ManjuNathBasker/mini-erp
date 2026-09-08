<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_order_calculates_item_subtotal_and_total(): void
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
                    'quantity' => 3,
                    'unit_price' => 100.00,
                ],
            ],
        ]);

        $this->assertEquals(300.00, (float) $purchaseOrder->total_amount);

        $this->assertEquals(
            300.00,
            (float) $purchaseOrder->items->first()->subtotal
        );
    }

    public function test_purchase_order_rejects_negative_quantity(): void
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

        $this->expectException(\InvalidArgumentException::class);

        app(PurchaseOrderService::class)->createPurchaseOrder([
            'supplier_id' => $supplier->id,
            'order_date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => -1,
                    'unit_price' => 100.00,
                ],
            ],
        ]);
    }
}
