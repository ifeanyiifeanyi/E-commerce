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
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title">Product Inventory: {{ $product->product_name }}</h4>
                                <a href="{{ route('admin.inventory') }}" class="btn btn-secondary">Back to List</a>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Product Details</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th style="width: 40%">Product Code</th>
                                                        <td>{{ $product->product_code }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Category</th>
                                                        <td>{{ $product->category ? $product->category->name : 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Brand</th>
                                                        <td>{{ $product->brand ? $product->brand->name : 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Measurement Unit</th>
                                                        <td>{{ $product->measurementUnit ? $product->measurementUnit->name . ' (' . $product->measurementUnit->symbol . ')' : 'N/A' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Stock Quantity</th>
                                                        <td>{{ $product->formattedQuantity() }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Reserved Quantity</th>
                                                        <td>{{ $product->reserved_qty }} {{ $product->formattedMeasure }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Available Quantity</th>
                                                        <td>{{ $product->available_qty }} {{ $product->formattedMeasure }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Status</th>
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
                                                                <span
                                                                    class="badge bg-info">{{ $product->stock_status }}</span>
                                                            @endif

                                                            @if ($product->isLowStock())
                                                                <span class="badge bg-warning text-dark">Low Stock</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Low Stock Threshold</th>
                                                        <td>{{ $product->low_stock_threshold }}
                                                            {{ $product->formattedMeasure }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Last Updated</th>
                                                        <td>{{ $product->stock_last_updated ? $product->stock_last_updated->format('Y-m-d H:i') : 'Never' }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Inventory Settings</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th style="width: 40%">Track Inventory</th>
                                                        <td>{{ $product->track_inventory ? 'Yes' : 'No' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Enable Stock Alerts</th>
                                                        <td>{{ $product->enable_stock_alerts ? 'Yes' : 'No' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Allow Backorders</th>
                                                        <td>{{ $product->allow_backorders ? 'Yes' : 'No' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Allow Decimal Quantity</th>
                                                        <td>{{ $product->allow_decimal_qty ? 'Yes' : 'No' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Weight Based Pricing</th>
                                                        <td>{{ $product->is_weight_based ? 'Yes' : 'No' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Min Order Quantity</th>
                                                        <td>{{ $product->min_order_qty }} {{ $product->formattedMeasure }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Max Order Quantity</th>
                                                        <td>{{ $product->max_order_qty ?? 'No limit' }}
                                                            {{ $product->max_order_qty ? $product->formattedMeasure : '' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Conversion Factor</th>
                                                        <td>{{ $product->conversion_factor ?? 'N/A' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Unresolved Alerts -->
                            @if ($unresolved_alerts->count() > 0)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h5 class="card-title text-danger">Unresolved Alerts</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Alert Type</th>
                                                                <th>Created</th>
                                                                <th>Notes</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($unresolved_alerts as $alert)
                                                                <tr>
                                                                    <td>
                                                                        @if ($alert->alert_type == 'low_stock')
                                                                            <span class="badge bg-warning text-dark">Low
                                                                                Stock</span>
                                                                        @elseif($alert->alert_type == 'out_of_stock')
                                                                            <span class="badge bg-danger">Out of
                                                                                Stock</span>
                                                                        @elseif($alert->alert_type == 'restock')
                                                                            <span class="badge bg-success">Restock</span>
                                                                        @else
                                                                            <span
                                                                                class="badge bg-info">{{ $alert->alert_type }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $alert->created_at->format('Y-m-d H:i') }}</td>
                                                                    <td>{{ $alert->notes }}</td>
                                                                    <td>
                                                                        <form
                                                                            action="{{ route('admin.inventory.alerts.resolve', $alert->id) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-success">Mark
                                                                                Resolved</button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Adjust Inventory Form -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Adjust Inventory</h5>
                                            <form action="{{ route('admin.inventory.adjust', $product->id) }}"
                                                method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="quantity_change" class="form-label">Quantity Change</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="quantity_change"
                                                            name="quantity_change"
                                                            step="{{ $product->allow_decimal_qty ? '0.01' : '1' }}"
                                                            required>
                                                        <span
                                                            class="input-group-text">{{ $product->formattedMeasure }}</span>
                                                    </div>
                                                    <div class="form-text">Use positive values to increase stock, negative
                                                        to decrease.</div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="action_type" class="form-label">Action Type</label>
                                                    <select class="form-select" id="action_type" name="action_type"
                                                        required>
                                                        <option value="purchase">Purchase</option>
                                                        <option value="sale">Sale</option>
                                                        <option value="return">Return</option>
                                                        <option value="damage">Damage/Loss</option>
                                                        <option value="count">Inventory Count</option>
                                                        <option value="adjustment">Other Adjustment</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="reference_type" class="form-label">Reference Type
                                                        (Optional)</label>
                                                    <input type="text" class="form-control" id="reference_type"
                                                        name="reference_type" placeholder="e.g., Order, Invoice, etc.">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="reference_id" class="form-label">Reference ID
                                                        (Optional)</label>
                                                    <input type="text" class="form-control" id="reference_id"
                                                        name="reference_id" placeholder="e.g., Order #123, Invoice #456">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="notes" class="form-label">Notes</label>
                                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                                </div>

                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary">Apply
                                                        Adjustment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Manage Reserved Inventory</h5>

                                            <div class="mb-4">
                                                <h6>Reserve Inventory</h6>
                                                <form action="{{ route('admin.inventory.reserve', $product->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="reserve_quantity" class="form-label">Quantity to
                                                            Reserve</label>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control"
                                                                id="reserve_quantity" name="quantity"
                                                                step="{{ $product->allow_decimal_qty ? '0.01' : '1' }}"
                                                                min="0.01" required>
                                                            <span
                                                                class="input-group-text">{{ $product->formattedMeasure }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="order_id" class="form-label">Order ID
                                                            (Optional)</label>
                                                        <input type="text" class="form-control" id="order_id"
                                                            name="order_id">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="reserve_notes" class="form-label">Notes</label>
                                                        <textarea class="form-control" id="reserve_notes" name="notes" rows="2"></textarea>
                                                    </div>

                                                    <div class="d-grid">
                                                        <button type="submit" class="btn btn-warning">Reserve
                                                            Inventory</button>
                                                    </div>
                                                </form>
                                            </div>

                                            <div>
                                                <h6>Release Reserved Inventory</h6>
                                                <form action="{{ route('admin.inventory.release', $product->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label for="release_quantity" class="form-label">Quantity to
                                                            Release</label>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control"
                                                                id="release_quantity" name="quantity"
                                                                step="{{ $product->allow_decimal_qty ? '0.01' : '1' }}"
                                                                min="0.01" max="{{ $product->reserved_qty }}"
                                                                required>
                                                            <span
                                                                class="input-group-text">{{ $product->formattedMeasure }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="release_order_id" class="form-label">Order ID
                                                            (Optional)</label>
                                                        <input type="text" class="form-control" id="release_order_id"
                                                            name="order_id">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="release_notes" class="form-label">Notes</label>
                                                        <textarea class="form-control" id="release_notes" name="notes" rows="2"></textarea>
                                                    </div>

                                                    <div class="d-grid">
                                                        <button type="submit" class="btn btn-info">Release Reserved
                                                            Inventory</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Inventory Logs -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title">Recent Inventory Logs</h5>
                                                <a href="{{ route('admin.inventory.logs', ['product' => $product->id]) }}"
                                                    class="btn btn-sm btn-info">View All Logs</a>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Date/Time</th>
                                                            <th>Action Type</th>
                                                            <th>Change</th>
                                                            <th>Previous</th>
                                                            <th>New</th>
                                                            <th>Reference</th>
                                                            <th>User</th>
                                                            <th>Notes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($recentLogs as $log)
                                                            <tr>
                                                                <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                                                <td>
                                                                    @if ($log->action_type == 'purchase')
                                                                        <span class="badge bg-success">Purchase</span>
                                                                    @elseif($log->action_type == 'sale')
                                                                        <span class="badge bg-danger">Sale</span>
                                                                    @elseif($log->action_type == 'return')
                                                                        <span class="badge bg-info">Return</span>
                                                                    @elseif($log->action_type == 'damage')
                                                                        <span
                                                                            class="badge bg-warning text-dark">Damage</span>
                                                                    @elseif($log->action_type == 'count')
                                                                        <span class="badge bg-primary">Count</span>
                                                                    @elseif($log->action_type == 'adjustment')
                                                                        <span class="badge bg-secondary">Adjustment</span>
                                                                    @elseif($log->action_type == 'reserve')
                                                                        <span
                                                                            class="badge bg-warning text-dark">Reserve</span>
                                                                    @else
                                                                        <span
                                                                            class="badge bg-secondary">{{ $log->action_type }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $log->quantity_change > 0 ? '+' : '' }}{{ $log->quantity_change }}
                                                                    {{ $product->formattedMeasure }}</td>
                                                                <td>{{ $log->previous_quantity }}
                                                                    {{ $product->formattedMeasure }}</td>
                                                                <td>{{ $log->new_quantity }}
                                                                    {{ $product->formattedMeasure }}</td>
                                                                <td>
                                                                    @if ($log->reference_type && $log->reference_id)
                                                                        {{ $log->reference_type }}:
                                                                        {{ $log->reference_id }}
                                                                    @elseif($log->reference_type)
                                                                        {{ $log->reference_type }}
                                                                    @elseif($log->reference_id)
                                                                        {{ $log->reference_id }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                                                                <td>{{ $log->notes ?? '-' }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="8" class="text-center">No inventory logs
                                                                    found</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
