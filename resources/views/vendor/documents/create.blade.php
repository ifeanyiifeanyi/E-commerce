@extends('vendor.layouts.vendor')

@section('title', 'Upload Document')

@section('vendor')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upload Verification Document</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Document Verification Process</h5>
                                <p>Please upload clear and legible copies of your business documents for verification. All submitted documents will be reviewed by our team, and you will be notified of the approval status.</p>
                                <p>Acceptable file formats: PDF, JPG, JPEG, PNG (Maximum size: 10MB)</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('vendor.documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="document_type">Document Type <span class="text-danger">*</span></label>
                            <select name="document_type" id="document_type" class="form-control @error('document_type') is-invalid @enderror" required>
                                <option value="">Select Document Type</option>
                                @foreach($documentTypes as $key => $value)
                                    <option value="{{ $key }}" {{ old('document_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('document_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="document_number">Document Number (if applicable)</label>
                            <input type="text" name="document_number" id="document_number" class="form-control @error('document_number') is-invalid @enderror" value="{{ old('document_number') }}">
                            @error('document_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="expiry_date">Expiry Date (if applicable)</label>
                            <input type="date" name="expiry_date" id="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror" value="{{ old('expiry_date') }}">
                            @error('expiry_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="document_file">Document File <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" name="document_file" id="document_file" class="custom-file-input @error('document_file') is-invalid @enderror" required>
                                <label class="custom-file-label" for="document_file">Choose file</label>
                                @error('document_file')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Accepted formats: PDF, JPG, JPEG, PNG (Max: 10MB)</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Upload Document</button>
                            <a href="{{ route('vendor.documents') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Show file name in custom file input
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    });
</script>
@endsection
