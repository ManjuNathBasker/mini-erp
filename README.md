# Mini ERP - Inventory & Purchase Order Management

A Laravel-based Mini ERP system for managing suppliers, inventory, and purchase orders with a controlled purchase order lifecycle.

## Tech Stack

- Laravel 13
- PHP 8.3+
- MySQL
- Laravel Sanctum
- Blade
- Bootstrap 5
- PHPUnit

## Features

- Supplier management
- Product/inventory management
- Low-stock inventory tracking
- Purchase order creation
- Purchase order lifecycle management
- Automatic stock updates when a PO is received
- REST API with Sanctum authentication
- Supplier spending report
- Database transactions and row-level locking
- Automated tests for business rules and API validation

## Purchase Order Lifecycle

Purchase orders follow these states:

```text
DRAFT → APPROVED → RECEIVED
   ↓
CANCELLED