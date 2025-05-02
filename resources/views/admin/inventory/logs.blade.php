@extends('admin.layouts.admin')

@section('title', 'Inventory Dashboard')

@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-parent-route', route('admin.dashboard'))

@section('admin-content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Inventory Logs: {{ $product->name }}</h1>
            <a href="{{ route('admin.inventory') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Inventory
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Product Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <p class="mb-1 font-weight-bold">SKU</p>
                        <p>{{ $product->sku }}</p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1 font-weight-bold">Current Stock</p>
                        <p>{{ $product->product_qty }}</p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1 font-weight-bold">Available</p>
                        <p>{{ $product->available_qty }}</p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1 font-weight-bold">Status</p>
                        <p>
                            @if ($product->stock_status == 'in_stock')
                                <span class="badge bg-success">In Stock</span>
                            @elseif($product->stock_status == 'out_of_stock')
                                <span class="badge bg-danger">Out of Stock</span>
                            @elseif($product->stock_status == 'backordered')
                                <span class="badge bg-warning text-dark">Backordered</span>
                            @elseif($product->stock_status == 'discontinued')
                                <span class="badge bg-secondary">Discontinued</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Inventory Logs</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="logsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Change</th>
                                <th>Previous</th>
                                <th>New</th>
                                <th>Reference</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                                    <td>
                                        @switch($log->action_type)
                                            @case('purchase')
                                                <span class="badge bg-success">Purchase</span>
                                            @break

                                            @case('sale')
                                                <span class="badge bg-primary">Sale</span>
                                            @break

                                            @case('adjustment')
                                                <span class="badge bg-info">Adjustment</span>
                                            @break

                                            @case('return')
                                                <span class="badge bg-warning text-dark">Return</span>
                                            @break

                                            @case('count')
                                                <span class="badge bg-secondary">Count</span>
                                            @break

                                            @case('reserve')
                                                <span class="badge bg-dark">Reserve</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">{{ $log->action_type }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if ($log->quantity_change > 0)
                                            <span class="text-success">+{{ $log->quantity_change }}</span>
                                        @elseif($log->quantity_change < 0)
                                            <span class="text-danger">{{ $log->quantity_change }}</span>
                                        @else
                                            <span>0</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->previous_quantity }}</td>
                                    <td>{{ $log->new_quantity }}</td>
                                    <td>
                                        @if ($log->reference_type && $log->reference_id)
                                            {{ $log->reference_type }}: {{ $log->reference_id }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $log->notes }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>



    @section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable
            $('#logsTable').DataTable({
                "paging": false,
                "ordering": true,
                "info": false,
            });
        });
    </script>
    @endsection
@endsection
