@extends('admin.layouts.admin')

@section('title', 'Store Documents - ' . $store->store_name)

@section('breadcrumb-parent', 'Vendor Stores')
@section('breadcrumb-parent-route', route('admin.vendor.stores'))
@section('breadcrumb-current', 'Store Documents')

@section('admin-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Documents for {{ $store->store_name }}</h4>
                        <div>
                            <a href="{{ route('admin.vendor.stores.show', $store) }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Back to Store
                            </a>
                        </div>
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

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Store Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 30%">Store Name</th>
                                                <td>{{ $store->store_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Vendor Name</th>
                                                <td>{{ $user->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
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
                                            </tr>
                                            <tr>
                                                <th>Tax Number</th>
                                                <td>{{ $store->tax_number ?: 'Not provided' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Banking Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th style="width: 30%">Bank Name</th>
                                                <td>{{ $store->bank_name ?: 'Not provided' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Account Name</th>
                                                <td>{{ $store->bank_account_name ?: 'Not provided' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Account Number</th>
                                                <td>{{ $store->bank_account_number ?: 'Not provided' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Routing Number</th>
                                                <td>{{ $store->bank_routing_number ?: 'Not provided' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Uploaded Documents</h5>
                                    </div>
                                    <div class="card-body">
                                        @if ($user->documents->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Document Type</th>
                                                            <th>Document Number</th>
                                                            <th>Expiry Date</th>
                                                            <th>Status</th>
                                                            <th>Uploaded</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($user->documents as $index => $document)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $document->document_type }}</td>
                                                                <td>{{ $document->document_number ?: 'N/A' }}</td>
                                                                <td>
                                                                    {{ $document->expiry_date ? $document->expiry_date->format('M d, Y') : 'N/A' }}
                                                                    @if ($document->expiry_date && $document->expiry_date->isPast())
                                                                        <span class="badge bg-danger">Expired</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($document->status == 'pending')
                                                                        <span class="badge bg-warning">Pending</span>
                                                                    @elseif($document->status == 'approved')
                                                                        <span class="badge bg-success">Approved</span>
                                                                    @elseif($document->status == 'rejected')
                                                                        <span class="badge bg-danger">Rejected</span>
                                                                    @else
                                                                        <span
                                                                            class="badge bg-secondary">{{ $document->status }}</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $document->created_at->format('M d, Y') }}</td>
                                                                <td>
                                                                    <div class="btn-group" role="group">
                                                                        <a href="{{ asset('storage/' . $document->file_path) }}"
                                                                            target="_blank" class="btn btn-sm btn-info">
                                                                            <i class="fas fa-eye"></i> View
                                                                        </a>

                                                                        @if ($document->status == 'pending')
                                                                            <form
                                                                                action="{{ route('admin.vendors.documents.approve', [$user, $document]) }}"
                                                                                method="POST" class="d-inline">
                                                                                @csrf
                                                                                <button type="submit"
                                                                                    class="btn btn-sm btn-success">
                                                                                    <i class="fas fa-check"></i> Approve
                                                                                </button>
                                                                            </form>

                                                                            <button type="button"
                                                                                class="btn btn-sm btn-danger"
                                                                                data-toggle="modal"
                                                                                data-target="#rejectModal{{ $document->id }}">
                                                                                <i class="fas fa-times"></i> Reject
                                                                            </button>

                                                                            <!-- Reject Modal -->
                                                                            <div class="modal fade"
                                                                                id="rejectModal{{ $document->id }}"
                                                                                tabindex="-1" role="dialog">
                                                                                <div class="modal-dialog" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title">Reject
                                                                                                Document</h5>
                                                                                            <button type="button"
                                                                                                class="close"
                                                                                                data-dismiss="modal"
                                                                                                aria-label="Close">
                                                                                                <span
                                                                                                    aria-hidden="true">&times;</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <form
                                                                                            action="{{ route('admin.vendors.documents.reject', [$user, $document]) }}"
                                                                                            method="POST">
                                                                                            @csrf
                                                                                            <div class="modal-body">
                                                                                                <div class="form-group">
                                                                                                    <label
                                                                                                        for="rejection_reason">Reason
                                                                                                        for
                                                                                                        Rejection</label>
                                                                                                    <textarea name="rejection_reason" id="rejection_reason" rows="3" class="form-control" required></textarea>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button"
                                                                                                    class="btn btn-secondary"
                                                                                                    data-dismiss="modal">Cancel</button>
                                                                                                <button type="submit"
                                                                                                    class="btn btn-danger">Reject
                                                                                                    Document</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif

                                                                        <form
                                                                            action="{{ route('admin.vendors.documents.destroy', [$user, $document]) }}"
                                                                            method="POST" class="d-inline"
                                                                            onsubmit="return confirm('Are you sure you want to delete this document?')">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-danger">
                                                                                <i class="fas fa-trash"></i> Delete
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                No documents have been uploaded for this vendor yet.
                                            </div>
                                        @endif
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

@section('css')
    <style>
        .badge {
            padding: 0.5em 0.75em;
        }

        .btn-group .btn {
            margin-right: 2px;
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
