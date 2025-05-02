@extends('admin.layouts.admin')

@section('title', 'Inventory Dashboard')

@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-parent-route', route('admin.dashboard'))

@section('admin-content')

    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Inventory Alerts</h1>
            <a href="{{ route('admin.inventory') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Inventory
            </a>
        </div>

        <!-- Filters -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.inventory.alerts') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="alert_type" class="form-label">Alert Type</label>
                        <select class="form-select" id="alert_type" name="alert_type">
                            <option value="">All Types</option>
                            <option value="low_stock" {{ request('alert_type') == 'low_stock' ? 'selected' : '' }}>Low Stock
                            </option>
                            <option value="out_of_stock" {{ request('alert_type') == 'out_of_stock' ? 'selected' : '' }}>Out
                                of Stock</option>
                            <option value="restock" {{ request('alert_type') == 'restock' ? 'selected' : '' }}>Restock
                            </option>
                            <option value="expiring" {{ request('alert_type') == 'expiring' ? 'selected' : '' }}>Expiring
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved
                            </option>
                            <option value="unresolved" {{ request('status') == 'unresolved' ? 'selected' : '' }}>Unresolved
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="product" class="form-label">Product</label>
                        <input type="text" class="form-control" id="product" name="product"
                            value="{{ request('product') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="{{ route('admin.inventory.alerts') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Alerts Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Inventory Alerts</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="alertsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Created</th>
                                <th>Status</th>
                                <th>Resolved By</th>
                                <th>Resolved On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alerts as $alert)
                                <tr class="{{ $alert->is_resolved ? '' : 'table-warning' }}">
                                    <td>{{ $alert->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.products.edit', $alert->product_id) }}">
                                            {{ $alert->product->name }}
                                        </a>
                                    </td>
                                    <td>
                                        @switch($alert->alert_type)
                                            @case('low_stock')
                                                <span class="badge bg-warning text-dark">Low Stock</span>
                                            @break

                                            @case('out_of_stock')
                                                <span class="badge bg-danger">Out of Stock</span>
                                            @break

                                            @case('restock')
                                                <span class="badge bg-success">Restocked</span>
                                            @break

                                            @case('expiring')
                                                <span class="badge bg-info">Expiring</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">{{ $alert->alert_type }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $alert->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if ($alert->is_resolved)
                                            <span class="badge bg-success">Resolved</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Unresolved</span>
                                        @endif
                                    </td>
                                    <td>{{ $alert->resolvedByUser ? $alert->resolvedByUser->name : 'N/A' }}</td>
                                    <td>{{ $alert->resolved_at ? $alert->resolved_at->format('Y-m-d H:i') : 'N/A' }}</td>
                                    <td>
                                        @if (!$alert->is_resolved)
                                            <button type="button" class="btn btn-sm btn-success resolve-alert"
                                                data-bs-toggle="modal" data-bs-target="#resolveAlertModal"
                                                data-alert-id="{{ $alert->id }}"
                                                data-product-name="{{ $alert->product->name }}">
                                                <i class="fas fa-check"></i> Resolve
                                            </button>
                                        @else
                                            <form action="{{ route('admin.inventory.alerts.unresolve', $alert) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-warning"
                                                    onclick="return confirm('Are you sure you want to mark this alert as unresolved?')">
                                                    <i class="fas fa-undo"></i> Unresolve
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-info view-notes" data-bs-toggle="modal"
                                            data-bs-target="#viewNotesModal" data-notes="{{ $alert->notes }}"
                                            data-product-name="{{ $alert->product->name }}">
                                            <i class="fas fa-sticky-note"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $alerts->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Resolve Alert Modal -->
    <div class="modal fade" id="resolveAlertModal" tabindex="-1" aria-labelledby="resolveAlertModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.inventory.alerts.resolve') }}" method="POST">
                    @csrf
                    <input type="hidden" name="alert_id" id="resolveAlertId">
                    <div class="modal-header">
                        <h5 class="modal-title" id="resolveAlertModalLabel">Resolve Alert</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Product</label>
                            <input type="text" class="form-control" id="resolveProductName" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Resolution Notes</label>
                            <textarea class="form-control" id="resolutionNotes" name="notes" rows="3"></textarea>
                            <div class="form-text">Add any notes about how this alert was resolved.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Mark as Resolved</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Notes Modal -->
    <div class="modal fade" id="viewNotesModal" tabindex="-1" aria-labelledby="viewNotesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewNotesModalLabel">Alert Notes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <input type="text" class="form-control" id="notesProductName" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <div class="p-3 bg-light border rounded" id="notesContent">
                            <p class="text-muted font-italic">No notes available</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @section('js')

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize DataTable
                $('#alertsTable').DataTable({
                    "paging": false,
                    "ordering": true,
                    "info": false,
                });

                // Handle resolve alert modal
                const resolveBtns = document.querySelectorAll('.resolve-alert');
                resolveBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const alertId = this.getAttribute('data-alert-id');
                        const productName = this.getAttribute('data-product-name');

                        document.getElementById('resolveAlertId').value = alertId;
                        document.getElementById('resolveProductName').value = productName;
                    });
                });

                // Handle view notes modal
                const viewNotesBtns = document.querySelectorAll('.view-notes');
                viewNotesBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const notes = this.getAttribute('data-notes');
                        const productName = this.getAttribute('data-product-name');

                        document.getElementById('notesProductName').value = productName;

                        const notesContent = document.getElementById('notesContent');
                        if (notes && notes.trim() !== '') {
                            notesContent.innerHTML = notes.replace(/\n/g, '<br>');
                        } else {
                            notesContent.innerHTML =
                                '<p class="text-muted font-italic">No notes available</p>';
                        }
                    });
                });
            });
        </script>
    @endsection


@endsection
