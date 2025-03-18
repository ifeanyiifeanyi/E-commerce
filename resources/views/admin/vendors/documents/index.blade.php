@extends('admin.layouts.admin')

@section('title', 'Vendor Documents')

@section('breadcrumb-parent', 'Vendor Management')
@section('breadcrumb-parent-route', route('admin.vendors'))

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 card-title">Verification Documents for {{ $user->name }}</h5>
                    <div>
                        <a href="{{ route('admin.vendors.documents.create', $user) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Upload New Document
                        </a>
                        <a href="{{ route('admin.vendors.show', $user) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Vendor
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Document Type</th>
                                    <th>Document Number</th>
                                    <th>Uploaded On</th>
                                    <th>Expiry Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($user->documents as $document)
                                    <tr>
                                        <td>{{ $document->document_type }}</td>
                                        <td>{{ $document->document_number ?: 'N/A' }}</td>
                                        <td>{{ $document->created_at->format('M d, Y') }}</td>
                                        <td>{{ $document->expiry_date ? $document->expiry_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            @if ($document->status === 'pending')
                                                <span class="badge bg-warning">Pending Review</span>
                                            @elseif ($document->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger" data-bs-toggle="tooltip" title="{{ $document->rejection_reason }}">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.vendors.documents.show', ['user' => $user->id, 'document' => $document->id]) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>

                                                @if ($document->status === 'pending')
                                                    <form method="POST" action="{{ route('admin.vendors.documents.approve', ['user' => $user->id, 'document' => $document->id]) }}" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                    </form>

                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $document->id }}">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                @endif

                                                <form action="{{ route('admin.vendors.documents.destroy', ['user' => $user->id, 'document' => $document->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this document?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Rejection Modal -->
                                            <div class="modal fade" id="rejectModal{{ $document->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $document->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="rejectModalLabel{{ $document->id }}">Reject Document</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form method="POST" action="{{ route('admin.vendors.documents.reject', ['user' => $user->id, 'document' => $document->id]) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="rejection_reason" class="form-label">Reason for Rejection</label>
                                                                    <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">Reject Document</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No documents uploaded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endsection
