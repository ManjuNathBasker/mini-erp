<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $suppliers = Supplier::insert([
            [
                'name' => 'ABC Supplies',
                'email' => 'abc@example.com',
                'phone' => '9876543210',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Global Traders',
                'email' => 'global@example.com',
                'phone' => '9876543211',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Prime Distributors',
                'email' => 'prime@example.com',
                'phone' => '9876543212',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $products = [
            ['sku' => 'SKU-001', 'name' => 'Laptop', 'unit_price' => 55000, 'stock_quantity' => 25, 'low_stock_threshold' => 10],
            ['sku' => 'SKU-002', 'name' => 'Wireless Mouse', 'unit_price' => 750, 'stock_quantity' => 8, 'low_stock_threshold' => 10],
            ['sku' => 'SKU-003', 'name' => 'Keyboard', 'unit_price' => 1200, 'stock_quantity' => 15, 'low_stock_threshold' => 5],
            ['sku' => 'SKU-004', 'name' => 'Monitor', 'unit_price' => 12000, 'stock_quantity' => 4, 'low_stock_threshold' => 5],
            ['sku' => 'SKU-005', 'name' => 'USB Cable', 'unit_price' => 300, 'stock_quantity' => 30, 'low_stock_threshold' => 10],
            ['sku' => 'SKU-006', 'name' => 'Webcam', 'unit_price' => 2500, 'stock_quantity' => 3, 'low_stock_threshold' => 5],
            ['sku' => 'SKU-007', 'name' => 'Headset', 'unit_price' => 1800, 'stock_quantity' => 12, 'low_stock_threshold' => 5],
            ['sku' => 'SKU-008', 'name' => 'Printer', 'unit_price' => 15000, 'stock_quantity' => 2, 'low_stock_threshold' => 5],
            ['sku' => 'SKU-009', 'name' => 'HDMI Cable', 'unit_price' => 500, 'stock_quantity' => 20, 'low_stock_threshold' => 8],
            ['sku' => 'SKU-010', 'name' => 'Laptop Stand', 'unit_price' => 2200, 'stock_quantity' => 6, 'low_stock_threshold' => 10],
        ];

        foreach ($products as &$product) {
            $product['is_active'] = true;
            $product['created_at'] = now();
            $product['updated_at'] = now();
        }

        Product::insert($products);
    }
}