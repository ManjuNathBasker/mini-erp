<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Mini ERP</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .table-warning-subtle { background-color: #fff3cd !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}"><i class="bi bi-box-seam me-2"></i>Mini ERP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('inventory.index') }}">Inventory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white ms-lg-2 px-3" href="{{ route('purchase-orders.create') }}">+ New PO</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Inventory Management</h2>
                <p class="text-muted mb-0">Overview of active products and stock levels</p>
            </div>
            <div>
                <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Create Purchase Order
                </a>
            </div>
        </div>

        <!-- Inventory Table Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0">Active Products</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>SKU</th>
                            <th>Product Name</th>
                            <th>Unit Price</th>
                            <th>Stock Quantity</th>
                            <th>Low Stock Threshold</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr class="{{ $product->is_low_stock ? 'table-warning-subtle' : '' }}">
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $product->sku }}</span>
                                </td>
                                <td class="fw-semibold">{{ $product->name }}</td>
                                <td>₹{{ number_format($product->unit_price, 2) }}</td>
                                <td>
                                    <span class="fw-bold {{ $product->is_low_stock ? 'text-danger' : 'text-dark' }}">
                                        {{ number_format($product->stock_quantity) }}
                                    </span>
                                </td>
                                <td>{{ number_format($product->low_stock_threshold) }}</td>
                                <td>
                                    @if($product->is_low_stock)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Low Stock
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle-fill me-1"></i> In Stock
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No active products found in inventory.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
