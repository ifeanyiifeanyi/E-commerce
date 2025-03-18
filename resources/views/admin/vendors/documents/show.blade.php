@extends('admin.layouts.admin')

@section('title', 'Document Details')

@section('breadcrumb-parent', 'Vendor Documents')
@section('breadcrumb-parent-route', route('admin.vendors.documents', $user))

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 card-title">{{ $document->document_type }} - Details</h5>
                    <div>
                        <a href="{{ route('admin.vendors.documents', $user) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Documents
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Document Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label fw-bold">Document Type:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext">{{ $document->document_type }}</p>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label fw-bold">Document Number:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext">{{ $document->document_number ?: 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label fw-bold">Uploaded On:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext">{{ $document->created_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label fw-bold">Expiry Date:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext">{{ $document->expiry_date ? $document->expiry_date->format('M d, Y') : 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label fw-bold">Status:</label>
                                        <div class="col-sm-8">
                                            @if ($document->status === 'pending')
                                                <span class="badge bg-warning">Pending Review</span>
                                            @elseif ($document->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($document->status === 'rejected' && $document->rejection_reason)
                                        <div class="mb-3 row">
                                            <label class="col-sm-4 col-form-label fw-bold">Rejection Reason:</label>
                                            <div class="col-sm-8">
                                                <div class="alert alert-danger mb-0 py-2">
                                                    {{ $document->rejection_reason }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ asset('storage/' . $document->file_path) }}" class="btn btn-primary" target="_blank">
                                            <i class="fas fa-file-download"></i> View/Download Document
                                        </a>

                                        @if ($document->status === 'pending')
                                            <div class="row">
                                                <div class="col">
                                                    <form method="POST" action="{{ route('admin.vendors.documents.approve', ['user' => $user->id, 'document' => $document->id]) }}" class="d-grid">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="fas fa-check"></i> Approve Document
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="col">
                                                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                                        <i class="fas fa-times"></i> Reject Document
                                                    </button>
                                                </div>
                                            </div>
                                        @endif

                                        <form action="{{ route('admin.vendors.documents.destroy', ['user' => $user->id, 'document' => $document->id]) }}" method="POST" class="d-grid" onsubmit="return confirm('Are you sure you want to delete this document?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-trash"></i> Delete Document
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Document Preview</h6>
                                </div>
                                <div class="card-body text-center">
                                    @php
                                        $fileExtension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                                        $isPdf = strtolower($fileExtension) === 'pdf';
                                    @endphp

                                    @if ($isPdf)
                                        <div class="ratio ratio-1x1">
                                            <iframe src="{{ asset('storage/' . $document->file_path) }}" title="Document Preview" allowfullscreen></iframe>
                                        </div>
                                    @else
                                        <img src="{{ asset('storage/' . $document->file_path) }}" alt="Document Preview" class="img-fluid mb-3 border" style="max-height: 600px;">
                                    @endif

                                    <div class="mt-3">
                                        <a href="{{ asset('storage/' . $document->file_path) }}" class="btn btn-sm btn-primary" target="_blank">
                                            Open in New Tab
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.vendors.documents.reject', ['user' => $user->id, 'document' => $document->id]) }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="rejection_reason" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" required placeholder="Please provide a detailed reason for rejecting this document..."></textarea>
                            <small class="form-text text-muted">This message will be visible to the vendor.</small>
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
@endsection
