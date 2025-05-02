@extends('vendor.layouts.vendor')

@section('title', 'My Documents')

@section('vendor')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Document Status Alerts -->
                @php
                    $documents = auth()->user()->documents;
                    $pendingDocuments = $documents->where('status', 'pending')->count();
                    $rejectedDocuments = $documents->where('status', 'rejected')->count();
                @endphp

                @if ($documents->isEmpty())
                    <div class="alert alert-warning">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-alt me-3 fa-2x"></i>
                            <div>
                                <h4 class="alert-heading">Required Documents Missing!</h4>
                                <p>You need to upload the required verification documents to complete your vendor profile.
                                </p>
                                <a href="{{ route('vendor.documents') }}" class="btn btn-primary"><i class="fas fa-file-upload"></i> Upload Documents</a>
                            </div>
                        </div>
                    </div>
                @elseif($rejectedDocuments > 0)
                    <div class="alert alert-danger">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-excel me-3 fa-2x"></i>
                            <div>
                                <h4 class="alert-heading">Document Issues Detected!</h4>
                                <p>{{ $rejectedDocuments }} document(s) have been rejected. Please review and upload new
                                    versions.</p>
                                <a href="{{ route('vendor.documents.create') }}" class="btn btn-danger">Review Documents</a>
                            </div>
                        </div>
                    </div>
                @elseif($pendingDocuments > 0)
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-upload me-3 fa-2x"></i>
                            <div>
                                <h4 class="alert-heading">Documents Under Review</h4>
                                <p>{{ $pendingDocuments }} document(s) are currently under review. We'll notify you once the
                                    review is complete.</p>

                            </div>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Verification Documents</h3>
                        <a href="{{ route('vendor.documents.create') }}" class="btn btn-primary">
                            <i class="fas fa-file-upload"></i> Upload
                        </a>
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

                        @if ($documents->isEmpty())
                            <div class="alert alert-info">
                                <p>You haven't uploaded any verification documents yet. Please upload the necessary
                                    documents to complete your vendor verification.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Document Type</th>
                                            <th>Document Number</th>
                                            <th>Status</th>
                                            <th>Submitted Date</th>
                                            <th>Expiry Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($documents as $document)
                                            <tr>
                                                <td>{{ $document->document_type }}</td>
                                                <td>{{ $document->document_number ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($document->status == 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($document->status == 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @else
                                                        <span class="badge bg-danger">Rejected</span>
                                                        @if ($document->rejection_reason)
                                                            <i class="fas fa-info-circle" data-toggle="tooltip"
                                                                title="{{ $document->rejection_reason }}"></i>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>{{ $document->created_at->format('M d, Y') }}</td>
                                                <td>{{ $document->expiry_date ? $document->expiry_date->format('M d, Y') : 'N/A' }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('vendor.documents.show', $document) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    @if ($document->status != 'approved')
                                                        <form action="{{ route('vendor.documents.destroy', $document) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Are you sure you want to delete this document?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
