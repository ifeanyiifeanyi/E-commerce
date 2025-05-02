@extends('admin.layouts.admin')

@section('title', 'Inventory Dashboard')

@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-parent-route', route('admin.dashboard'))

@section('admin-content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Inventory Management</h4>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <form action="{{ route('admin.inventory') }}" method="GET" class="d-flex flex-wrap">
                                        <div class="input-group me-2 mb-2" style="width: auto;">
                                            <input type="text" class="form-control" name="search"
                                                placeholder="Search product..." value="{{ request('search') }}">
                                            <button class="btn btn-primary" type="submit">Search</button>
                                        </div>

                                        <div class="d-flex flex-wrap">
                                            <select name="stock_status" class="form-select me-2 mb-2" style="width: auto;">
                                                <option value="all"
                                                    {{ request('stock_status') == 'all' ? 'selected' : '' }}>All Status
                                                </option>
                                                <option value="in_stock"
                                                    {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock
                                                </option>
                                                <option value="out_of_stock"
                                                    {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of
                                                    Stock</option>
                                                <option value="backordered"
                                                    {{ request('stock_status') == 'backordered' ? 'selected' : '' }}>
                                                    Backordered</option>
                                                <option value="discontinued"
                                                    {{ request('stock_status') == 'discontinued' ? 'selected' : '' }}>
                                                    Discontinued</option>
                                            </select>

                                            <select name="sort" class="form-select me-2 mb-2" style="width: auto;">
                                                <option value="name_asc"
                                                    {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)
                                                </option>
                                                <option value="name_desc"
                                                    {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)
                                                </option>
                                                <option value="stock_low_to_high"
                                                    {{ request('sort') == 'stock_low_to_high' ? 'selected' : '' }}>Stock
                                                    (Low to High)</option>
                                                <option value="stock_high_to_low"
                                                    {{ request('sort') == 'stock_high_to_low' ? 'selected' : '' }}>Stock
                                                    (High to Low)</option>
                                                >
                                            </select>

                                            <div class="form-check me-2 mb-2 d-flex align-items-center">
                                                <input class="form-check-input" type="checkbox" name="low_stock"
                                                    value="true" id="lowStockCheck"
                                                    {{ request('low_stock') == 'true' ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2" for="lowStockCheck">
                                                    Low Stock Only
                                                </label>
                                            </div>

                                            <button type="submit" class="btn btn-secondary mb-2">Apply</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="{{ route('admin.inventory.alerts') }}" class="btn btn-warning me-2 mb-2">
                                        View Alerts
                                        @if ($unresolved_alerts = \App\Models\InventoryAlert::where('is_resolved', false)->count())
                                            <span class="badge bg-danger">{{ $unresolved_alerts }}</span>
                                        @endif
                                    </a>
                                    {{-- <a href="{{ route('admin.inventory.logs') }}" class="btn btn-info mb-2">View All
                                        Logs</a> --}}
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Code</th>
                                            <th>Category</th>
                                            <th>Stock Qty</th>
                                            <th>Reserved</th>
                                            <th>Available</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $product)
                                            <tr
                                                class="{{ $product->isLowStock() ? 'table-warning' : '' }} {{ $product->isOutOfStock() ? 'table-danger' : '' }}">
                                                <td>{{ $product->product_name }}</td>
                                                <td>{{ $product->product_code }}</td>
                                                <td>{{ $product->category ? $product->category->name : 'N/A' }}</td>
                                                <td>{{ $product->formattedQuantity() }}</td>
                                                <td>{{ $product->reserved_qty }} {{ $product->formattedMeasure }}</td>
                                                <td>{{ $product->available_qty }} {{ $product->formattedMeasure }}</td>
                                                <td>
                                                    @if ($product->stock_status == 'in_stock')
                                                        <span class="badge bg-success">In Stock</span>
                                                    @elseif($product->stock_status == 'out_of_stock')
                                                        <span class="badge bg-danger">Out of Stock</span>
                                                    @elseif($product->stock_status == 'backordered')
                                                        <span class="badge bg-warning text-dark">Backordered</span>
                                                    @elseif($product->stock_status == 'discontinued')
                                                        <span class="badge bg-secondary">Discontinued</span>
                                                    @else
                                                        <span class="badge bg-info">{{ $product->stock_status }}</span>
                                                    @endif

                                                    @if ($product->isLowStock())
                                                        <span class="badge bg-warning text-dark">Low Stock</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.inventory.show', $product->id) }}"
                                                        class="btn btn-sm btn-primary">Manage</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4">
                                {{ $products->appends(request()->all())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable
            $('#inventoryTable').DataTable({
                "paging": false,
                "searching": true,
                "ordering": true,
                "info": false,
            });

            // Handle adjust inventory modal
            const adjustBtns = document.querySelectorAll('.adjust-inventory');
            adjustBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const productName = this.getAttribute('data-product-name');

                    document.getElementById('adjustProductId').value = productId;
                    document.getElementById('adjustProductName').value = productName;
                });
            });
        });
    </script>
@endsection
@endsection
