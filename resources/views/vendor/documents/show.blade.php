@extends('vendor.layouts.vendor')

@section('title', 'Document Details')

@section('vendor')
    <div class="container-fluid" style="height: 100vh;">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Document Verification Process</h5>
                    <p>Please ensure that all documents are clear and legible. If you have any questions, please contact
                        support.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Document Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <th style="width: 30%">Document Type:</th>
                                        <td>{{ $document->document_type }}</td>
                                    </tr>
                                    <tr>
                                        <th>Document Number:</th>
                                        <td>{{ $document->document_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @if ($document->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($document->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Submitted On:</th>
                                        <td>{{ $document->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Expiry Date:</th>
                                        <td>{{ $document->expiry_date ? $document->expiry_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                </table>

                                @if ($document->status == 'rejected' && $document->rejection_reason)
                                    <div class="alert alert-danger mt-3">
                                        <h5><i class="icon fas fa-ban"></i> Rejection Reason</h5>
                                        <p>{{ $document->rejection_reason }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Document Preview</h5>
                                    </div>
                                    <div class="card-body text-center">
                                        @php
                                            $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);

                                            $publicPath = public_path($document->file_path);
                                        @endphp
    {{-- @dd($extension) --}}
                                        @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png']))
                                            <img src="{{ asset($document->file_path) }}" alt="Document" class="img-fluid">
                                        @elseif(strtolower($extension) === 'pdf')
                                            <div class="embed-responsive embed-responsive-16by9">
                                                <iframe class="embed-responsive-item"
                                                    src="{{ asset($document->file_path) }}" allowfullscreen></iframe>
                                            </div>
                                        @else
                                            <p>Preview not available for this file type.</p>
                                        @endif
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ Storage::url($document->file_path) }}" class="btn btn-primary btn-block"
                                            target="_blank">
                                            <i class="fas fa-download"></i> Download Document
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('vendor.documents') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Documents
                            </a>

                            @if ($document->status != 'approved')
                                <form action="{{ route('vendor.documents.destroy', $document) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this document?')">
                                        <i class="fas fa-trash"></i> Delete Document
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
