@extends('admin.layouts.admin')

@section('title', 'Upload Vendor Document')

@section('breadcrumb-parent', 'Vendor Documents')
@section('breadcrumb-parent-route', route('admin.vendors.documents', $user))

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Upload Verification Document for {{ $user->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.vendors.documents.store', $user) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="document_type" class="form-label">Document Type <span class="text-danger">*</span></label>
                                    <select name="document_type" id="document_type" class="form-select @error('document_type') is-invalid @enderror" required>
                                        <option value="">Select Document Type</option>
                                        @foreach($documentTypes as $value => $label)
                                            <option value="{{ $value }}" {{ old('document_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('document_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="document_number" class="form-label">Document Number/Reference <small class="text-muted">(if applicable)</small></label>
                                    <input type="text" name="document_number" id="document_number" class="form-control @error('document_number') is-invalid @enderror" value="{{ old('document_number') }}">
                                    @error('document_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="document_file" class="form-label">Document File <span class="text-danger">*</span></label>
                                    <input type="file" name="document_file" id="document_file" class="form-control @error('document_file') is-invalid @enderror" required accept=".pdf,.jpg,.jpeg,.png">
                                    @error('document_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Accepted formats: PDF, JPG, JPEG, PNG. Maximum size: 10MB</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expiry_date" class="form-label">Expiry Date <small class="text-muted">(if applicable)</small></label>
                                    <input type="date" name="expiry_date" id="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror" value="{{ old('expiry_date') }}">
                                    @error('expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">Important Information:</h6>
                                    <ul class="mb-0">
                                        <li>All documents must be clear, legible, and complete.</li>
                                        <li>Documents will be reviewed by our team and may be rejected if they don't meet our requirements.</li>
                                        <li>Sensitive information like bank details should be partially redacted for security.</li>
                                        <li>All submitted documents must be valid and not expired.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <a href="{{ route('admin.vendors.documents', $user) }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Upload Document</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
