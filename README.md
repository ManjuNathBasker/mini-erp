# Mini ERP - Inventory & Purchase Order Management

A Laravel-based Mini ERP system for managing suppliers, inventory, and purchase orders with a controlled purchase order lifecycle.

The application provides a web interface and REST API for inventory and purchase order management, with transaction-safe stock updates and automated tests for core business rules.

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
- Product and inventory management
- Low-stock inventory tracking
- Purchase order creation
- Dynamic purchase order line items
- Automatic subtotal and total calculation
- Purchase order lifecycle management
- Automatic stock updates when a PO is received
- REST API with Sanctum authentication
- Supplier spending report
- Database transactions
- Row-level locking for stock updates
- Database indexing and query optimization
- Automated tests for business rules and API validation

## Purchase Order Lifecycle

Purchase orders follow these states:

```text
DRAFT → APPROVED → RECEIVED
   ↓         ↓
CANCELLED  CANCELLED