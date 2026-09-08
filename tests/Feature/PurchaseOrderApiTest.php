<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseOrderApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_to_inventory_api_returns_401(): void
    {
        $response = $this->getJson('/api/inventory');

        $response->assertStatus(401);
    }

    public function test_invalid_purchase_order_data_returns_422(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/purchase-orders', [
                'supplier_id' => 999999,
                'order_date' => 'invalid-date',
                'items' => [],
            ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'supplier_id',
            'order_date',
            'items',
        ]);
    }
}