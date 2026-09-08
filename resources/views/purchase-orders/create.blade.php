<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Purchase Order - Mini ERP</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
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
                        <a class="nav-link" href="{{ route('inventory.index') }}">Inventory</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white ms-lg-2 px-3 active" href="{{ route('purchase-orders.create') }}">+ New PO</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Create Purchase Order</h2>
                <p class="text-muted mb-0">Create a new purchase order in DRAFT status</p>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
                <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-box me-1"></i> Inventory
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i>Please fix the following validation errors:</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form id="po-form" action="{{ route('purchase-orders.store') }}" method="POST">
            @csrf
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="supplier_id" class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                <option value="">-- Select Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="order_date" class="form-label fw-semibold">Order Date <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" id="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', date('Y-m-d')) }}" required>
                            @error('order_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="2" placeholder="Optional notes for this purchase order...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Line Items Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <h5 class="fw-bold mb-0">Line Items</h5>
                    <button type="button" id="btn-add-row" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Product
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40%;">Product <span class="text-danger">*</span></th>
                                <th style="width: 15%;">Quantity <span class="text-danger">*</span></th>
                                <th style="width: 20%;">Unit Price (₹) <span class="text-danger">*</span></th>
                                <th style="width: 20%;">Subtotal (₹)</th>
                                <th style="width: 5%;" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="items-table-body">
                            @php
                                $oldItems = old('items', [[]]);
                            @endphp
                            @foreach($oldItems as $index => $item)
                                <tr>
                                    <td>
                                        <select name="items[{{ $index }}][product_id]" class="form-select item-product-select" required>
                                            <option value="" data-price="0">-- Select Product --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}" {{ isset($item['product_id']) && $item['product_id'] == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }} (SKU: {{ $product->sku }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-qty" min="1" step="1" value="{{ $item['quantity'] ?? 1 }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $index }}][unit_price]" class="form-control item-price" min="0" step="0.01" value="{{ $item['unit_price'] ?? '0.00' }}" required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control item-subtotal bg-light" value="0.00" readonly>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove Item">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-end py-3">Total Amount:</td>
                                <td colspan="2" class="py-3 fs-5 text-primary" id="grand-total">$0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-light px-4 border">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Save Purchase Order
                </button>
            </div>
        </form>
    </div>

    <!-- Hidden Template Row for JS -->
    <template id="row-template">
        <tr>
            <td>
                <select name="items[__INDEX__][product_id]" class="form-select item-product-select" required>
                    <option value="" data-price="0">-- Select Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}">
                            {{ $product->name }} (SKU: {{ $product->sku }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[__INDEX__][quantity]" class="form-control item-qty" min="1" step="1" value="1" required>
            </td>
            <td>
                <input type="number" name="items[__INDEX__][unit_price]" class="form-control item-price" min="0" step="0.01" value="0.00" required>
            </td>
            <td>
                <input type="text" class="form-control item-subtotal bg-light" value="0.00" readonly>
            </td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove Item">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableBody = document.getElementById('items-table-body');
            const addRowBtn = document.getElementById('btn-add-row');
            const template = document.getElementById('row-template').innerHTML;

            function calculateRow(row) {
                const qtyInput = row.querySelector('.item-qty');
                const priceInput = row.querySelector('.item-price');
                const subtotalInput = row.querySelector('.item-subtotal');

                const qty = parseInt(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const subtotal = (qty * price).toFixed(2);

                subtotalInput.value = subtotal;
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let grandTotal = 0;
                document.querySelectorAll('.item-subtotal').forEach(function (input) {
                    grandTotal += parseFloat(input.value) || 0;
                });
                document.getElementById('grand-total').textContent = '₹' + grandTotal.toFixed(2);
            }

            function reindexRows() {
                const rows = tableBody.querySelectorAll('tr');
                rows.forEach(function (row, index) {
                    const select = row.querySelector('.item-product-select');
                    const qty = row.querySelector('.item-qty');
                    const price = row.querySelector('.item-price');

                    if (select) select.name = `items[${index}][product_id]`;
                    if (qty) qty.name = `items[${index}][quantity]`;
                    if (price) price.name = `items[${index}][unit_price]`;
                });
            }

            // Recalculate all rows on initial load (for old inputs)
            tableBody.querySelectorAll('tr').forEach(function (row) {
                calculateRow(row);
            });

            // Handle Product Selection Change
            tableBody.addEventListener('change', function (e) {
                if (e.target && e.target.classList.contains('item-product-select')) {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const price = selectedOption.getAttribute('data-price');
                    const row = e.target.closest('tr');
                    if (price !== null && price !== undefined) {
                        const priceInput = row.querySelector('.item-price');
                        priceInput.value = parseFloat(price).toFixed(2);
                        calculateRow(row);
                    }
                }
            });

            // Handle Quantity or Unit Price Input
            tableBody.addEventListener('input', function (e) {
                if (e.target && (e.target.classList.contains('item-qty') || e.target.classList.contains('item-price'))) {
                    const row = e.target.closest('tr');
                    if (row) calculateRow(row);
                }
            });

            // Add Row
            addRowBtn.addEventListener('click', function () {
                const rowCount = tableBody.querySelectorAll('tr').length;
                const newRowHtml = template.replace(/__INDEX__/g, rowCount);
                tableBody.insertAdjacentHTML('beforeend', newRowHtml);
                reindexRows();
            });

            // Remove Row
            tableBody.addEventListener('click', function (e) {
                const removeBtn = e.target.closest('.btn-remove-row');
                if (removeBtn) {
                    const rows = tableBody.querySelectorAll('tr');
                    if (rows.length <= 1) {
                        alert('A purchase order must contain at least one line item.');
                        return;
                    }
                    const row = removeBtn.closest('tr');
                    if (row) {
                        row.remove();
                        reindexRows();
                        calculateGrandTotal();
                    }
                }
            });

            // Form Submit Guard
            document.getElementById('po-form').addEventListener('submit', function (e) {
                const rows = tableBody.querySelectorAll('tr');
                if (rows.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one line item before submitting.');
                    return false;
                }
            });
        });
    </script>
</body>
</html>
