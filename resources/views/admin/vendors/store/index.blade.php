@extends('admin.layouts.admin')

@section('title', 'Manage Vendor Stores')

@section('breadcrumb-parent', 'Vendor Management')
@section('breadcrumb-parent-route', route('admin.vendors'))
@section('breadcrumb-current', 'Vendor Stores')

@section('admin-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Vendor Stores</h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover" id="datatables">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Store Name</th>
                                        <th>Vendor Name</th>
                                        <th>Status</th>
                                        <th>Featured</th>
                                        <th>Join Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stores as $store)
                                        <tr>
                                            <td>{{ $store->id }}</td>
                                            <td>{{ $store->store_name }}</td>
                                            <td>
                                                @if ($store->vendor)
                                                    <a href="{{ route('admin.vendors.show', $store->vendor) }}">
                                                        {{ $store->vendor->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No vendor</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($store->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($store->status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($store->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $store->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($store->is_featured)
                                                    <span class="badge bg-info">Featured</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>{{ $store->join_date ? $store->join_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.vendor.stores.show', $store) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('admin.vendor.stores.documents', $store) }}"
                                                        class="btn btn-sm btn-secondary">
                                                        <i class="fas fa-file-alt"></i> Documents
                                                    </a>
                                                    <form action="{{ route('admin.vendor.stores.destroy', $store) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this store?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No vendor stores found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $stores->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
   
    <style>
        .badge {
            padding: 0.5em 0.75em;
        }

        .btn-group .btn {
            margin-right: 5px;
        }
    </style>
@endsection

@section('js')


    <script>
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
