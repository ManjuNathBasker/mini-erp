<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $purchaseOrder->po_number }}</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body>

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a href="{{ route('dashboard') }}" class="navbar-brand">
            Mini ERP
        </a>

        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                Dashboard
            </a>
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-light btn-sm">
                Inventory
            </a>
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-light btn-sm">
                New Purchase Order
            </a>
        </div>
    </div>
</nav>

<div class="container">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1">Purchase Order</h1>
            <p class="text-muted mb-0">{{ $purchaseOrder->po_number }}</p>
        </div>

        @php
            $statusClass = match($purchaseOrder->status->value) {
                'DRAFT' => 'bg-secondary',
                'APPROVED' => 'bg-primary',
                'RECEIVED' => 'bg-success',
                'CANCELLED' => 'bg-danger',
                default => 'bg-secondary',
            };
        @endphp

        <span class="badge {{ $statusClass }} fs-6">
            {{ $purchaseOrder->status->value }}
        </span>
    </div>

    <div class="row g-4">

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <strong>Purchase Order Details</strong>
                </div>

                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">PO Number</dt>
                        <dd class="col-sm-7">{{ $purchaseOrder->po_number }}</dd>

                        <dt class="col-sm-5">Supplier</dt>
                        <dd class="col-sm-7">{{ $purchaseOrder->supplier->name }}</dd>

                        <dt class="col-sm-5">Order Date</dt>
                        <dd class="col-sm-7">
                            {{ $purchaseOrder->order_date->format('d M Y') }}
                        </dd>

                        <dt class="col-sm-5">Status</dt>
                        <dd class="col-sm-7">
                            <span class="badge {{ $statusClass }}">
                                {{ $purchaseOrder->status->value }}
                            </span>
                        </dd>

                        @if($purchaseOrder->notes)
                            <dt class="col-sm-5">Notes</dt>
                            <dd class="col-sm-7">{{ $purchaseOrder->notes }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <strong>Actions</strong>
                </div>

                <div class="card-body">

                    @if($purchaseOrder->status->value === 'DRAFT')

                        <p class="text-muted">
                            This purchase order can be approved or cancelled.
                        </p>

                        <div class="d-flex gap-2">

                            <form
                                method="POST"
                                action="{{ route('purchase-orders.status', $purchaseOrder) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <input type="hidden" name="status" value="APPROVED">

                                <button type="submit" class="btn btn-primary">
                                    Approve
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('purchase-orders.status', $purchaseOrder) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <input type="hidden" name="status" value="CANCELLED">

                                <button type="submit" class="btn btn-outline-danger">
                                    Cancel
                                </button>
                            </form>

                        </div>

                    @elseif($purchaseOrder->status->value === 'APPROVED')

                        <p class="text-muted">
                            This purchase order is approved and can now be received or cancelled.
                        </p>

                        <div class="d-flex gap-2">

                            <form
                                method="POST"
                                action="{{ route('purchase-orders.status', $purchaseOrder) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <input type="hidden" name="status" value="RECEIVED">

                                <button type="submit" class="btn btn-success">
                                    Mark as Received
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('purchase-orders.status', $purchaseOrder) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <input type="hidden" name="status" value="CANCELLED">

                                <button type="submit" class="btn btn-outline-danger">
                                    Cancel
                                </button>
                            </form>

                        </div>

                    @elseif($purchaseOrder->status->value === 'RECEIVED')

                        <div class="alert alert-success mb-0">
                            <strong>Received.</strong>
                            Stock has been updated for this purchase order.
                            This purchase order is now immutable.
                        </div>

                    @elseif($purchaseOrder->status->value === 'CANCELLED')

                        <div class="alert alert-danger mb-0">
                            <strong>Cancelled.</strong>
                            This purchase order is now immutable.
                        </div>

                    @endif

                </div>
            </div>
        </div>

    </div>

    <div class="card mt-4">
        <div class="card-header">
            <strong>Purchase Order Items</strong>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>SKU</th>
                            <th>Product</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($purchaseOrder->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    {{ $item->product->sku }}
                                </td>

                                <td>
                                    {{ $item->product->name }}
                                </td>

                                <td class="text-end">
                                    {{ $item->quantity }}
                                </td>

                                <td class="text-end">
                                    ₹{{ number_format($item->unit_price, 2) }}
                                </td>

                                <td class="text-end">
                                    ₹{{ number_format($item->subtotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">
                                Total
                            </th>
                            <th class="text-end">
                                ₹{{ number_format($purchaseOrder->total_amount, 2) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>

</div>

</body>
</html>