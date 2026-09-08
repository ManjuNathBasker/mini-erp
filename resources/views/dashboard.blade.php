<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mini ERP</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card-stat { border: none; border-radius: 8px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
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
                        <a class="nav-link active" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('inventory.index') }}">Inventory</a>
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
                <h2 class="fw-bold mb-1">ERP Overview</h2>
                <p class="text-muted mb-0">Inventory and Purchase Order Management</p>
            </div>
            <div>
                <a href="/purchase-orders/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Create Purchase Order</a>
            </div>
        </div>

        <!-- Metrics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-stat bg-white p-3 border-start border-primary border-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small fw-semibold">Active Products</div>
                            <h3 class="fw-bold text-dark mb-0">{{ number_format($activeProductsCount) }}</h3>
                        </div>
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                            <i class="bi bi-box fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-stat bg-white p-3 border-start border-warning border-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small fw-semibold">Low Stock Products</div>
                            <h3 class="fw-bold text-dark mb-0">{{ number_format($lowStockCount) }}</h3>
                        </div>
                        <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                            <i class="bi bi-exclamation-triangle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-stat bg-white p-3 border-start border-success border-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase text-muted small fw-semibold">Received Expenditure</div>
                            <h3 class="fw-bold text-dark mb-0">₹{{ number_format($receivedExpenditure, 2) }}</h3>
                        </div>
                        <div class="bg-success-subtle text-success p-3 rounded-circle">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Purchase Orders Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                <h5 class="fw-bold mb-0">Latest Purchase Orders</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestPurchaseOrders as $po)
                            <tr>
                                <td class="fw-semibold">{{ $po->po_number }}</td>
                                <td>{{ $po->supplier?->name ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($po->status?->value ?? (string)$po->status) {
                                            'DRAFT' => 'bg-secondary',
                                            'APPROVED' => 'bg-info text-dark',
                                            'RECEIVED' => 'bg-success',
                                            'CANCELLED' => 'bg-danger',
                                            default => 'bg-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $po->status?->value ?? $po->status }}
                                    </span>
                                </td>
                                <td>₹{{ number_format($po->total_amount, 2) }}</td>
                                <td>{{ $po->order_date?->format('Y-m-d') ?? 'N/A' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No purchase orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
