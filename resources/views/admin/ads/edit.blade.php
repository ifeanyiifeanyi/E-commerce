@extends('admin.layouts.admin')

@section('title', 'Edit Advertisement Package')
@section('breadcrumb-parent', 'Advertisement Packages')
@section('breadcrumb-parent-route', route('admin.advertisement.packages'))

@section('admin-content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Advertisement Package: {{ $package->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.advertisement.packages') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Packages
                        </a>
                    </div>
                </div>

                <form action="{{ route('admin.advertisement.packages.update', $package) }}" method="POST" id="packageForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="name" class="required">Package Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $package->name) }}" 
                                                   placeholder="e.g., Premium Home Banner" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="3" 
                                                      placeholder="Describe the package features and benefits">{{ old('description', $package->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="location" class="required">Location</label>
                                                    <select class="form-control @error('location') is-invalid @enderror" 
                                                            id="location" name="location" required
                                                            @if($package->activeAdvertisements()->count() > 0) disabled @endif>
                                                        <option value="">Select Location</option>
                                                        @foreach($locations as $key => $location)
                                                            <option value="{{ $key }}" 
                                                                    {{ old('location', $package->location) == $key ? 'selected' : '' }}
                                                                    data-description="{{ $location['description'] }}"
                                                                    data-size="{{ $location['recommended_size'] }}">
                                                                {{ $location['label'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('location')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted" id="locationHelp"></small>
                                                    @if($package->activeAdvertisements()->count() > 0)
                                                        <small class="text-warning">Cannot change location while active subscriptions exist</small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="sort_order">Sort Order</label>
                                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                           id="sort_order" name="sort_order" value="{{ old('sort_order', $package->sort_order) }}" 
                                                           min="0" placeholder="0">
                                                    @error('sort_order')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">Lower numbers appear first</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Features -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Package Features</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="features">Features</label>
                                            <div id="featuresContainer">
                                                @if(old('features', $package->features))
                                                    @foreach(old('features', $package->features) as $index => $feature)
                                                        <div class="input-group mb-2 feature-input">
                                                            <input type="text" class="form-control" name="features[]" 
                                                                   value="{{ $feature }}" placeholder="Enter feature">
                                                            <div class="input-group-append">
                                                                <button type="button" class="btn btn-danger remove-feature" 
                                                                        onclick="removeFeature(this)">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="input-group mb-2 feature-input">
                                                        <input type="text" class="form-control" name="features[]" 
                                                               placeholder="Enter feature (e.g., Prime placement)">
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-danger remove-feature" 
                                                                    onclick="removeFeature(this)">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-sm btn-success" onclick="addFeature()">
                                                <i class="fas fa-plus"></i> Add Feature
                                            </button>
                                            @error('features')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing & Settings -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Pricing & Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="price" class="required">Price ($)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                                       id="price" name="price" value="{{ old('price', $package->price) }}" 
                                                       step="0.01" min="0" placeholder="0.00" required
                                                       @if($package->activeAdvertisements()->count() > 0) readonly @endif>
                                            </div>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @if($package->activeAdvertisements()->count() > 0)
                                                <small class="text-warning">Cannot change price while active subscriptions exist</small>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="duration_days" class="required">Duration (Days)</label>
                                            <input type="number" class="form-control @error('duration_days') is-invalid @enderror" 
                                                   id="duration_days" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" 
                                                   min="1" max="365" placeholder="30" required
                                                   @if($package->activeAdvertisements()->count() > 0) readonly @endif>
                                            @error('duration_days')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">1-365 days</small>
                                            @if($package->activeAdvertisements()->count() > 0)
                                                <small class="text-warning">Cannot change duration while active subscriptions exist</small>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="max_slots" class="required">Maximum Slots</label>
                                            <input type="number" class="form-control @error('max_slots') is-invalid @enderror" 
                                                   id="max_slots" name="max_slots" value="{{ old('max_slots', $package->max_slots) }}" 
                                                   min="1" max="100" placeholder="5" required
                                                   @if($package->activeAdvertisements()->count() > 0) readonly @endif>
                                            @error('max_slots')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Concurrent advertisements allowed</small>
                                            @if($package->activeAdvertisements()->count() > 0)
                                                <small class="text-warning">Cannot change slots while active subscriptions exist</small>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="is_active" 
                                                       name="is_active" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Active Package
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">Only active packages can be purchased</small>
                                        </div>

                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="notify_vendors" 
                                                       name="notify_vendors" value="1">
                                                <label class="form-check-label" for="notify_vendors">
                                                    Notify Vendors
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">Send email notification to vendors about this package</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview Card -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Package Preview</h5>
                                    </div>
                                    <div class="card-body" id="packagePreview">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-eye fa-2x mb-2"></i>
                                            <p>Fill in the form to see preview</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Package
                                </button>
                                <button type="button" class="btn btn-secondary ml-2" onclick="resetForm()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('admin.advertisement.packages') }}" class="btn btn-light">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    .required::after {
        content: ' *';
        color: red;
    }
    .feature-input {
        animation: fadeIn 0.3s ease-in;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .package-preview-card {
        border: 2px dashed #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        background: #f8f9fa;
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Update location help text
        $('#location').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const description = selectedOption.data('description');
            const size = selectedOption.data('size');
            
            if (description) {
                $('#locationHelp').html(`${description}<br><strong>Recommended size:</strong> ${size}`);
            } else {
                $('#locationHelp').text('');
            }
            
            updatePreview();
        });

        // Update preview on form changes
        $('#name, #price, #duration_days, #max_slots, #location').on('input change', updatePreview);
        
        // Trigger initial update
        $('#location').trigger('change');
        updatePreview();
    });

    function addFeature() {
        const container = $('#featuresContainer');
        const newFeature = `
            <div class="input-group mb-2 feature-input">
                <input type="text" class="form-control" name="features[]" placeholder="Enter feature">
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-feature" onclick="removeFeature(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        container.append(newFeature);
    }

    function removeFeature(button) {
        $(button).closest('.feature-input').fadeOut(300, function() {
            $(this).remove();
        });
    }

    function updatePreview() {
        const name = $('#name').val() || 'Package Name';
        const price = $('#price').val() || '0.00';
        const duration = $('#duration_days').val() || '0';
        const slots = $('#max_slots').val() || '0';
        const location = $('#location option:selected').text() || 'Select Location';
        
        const features = [];
        $('input[name="features[]"]').each(function() {
            if ($(this).val().trim()) {
                features.push($(this).val().trim());
            }
        });

        let featuresHtml = '';
        if (features.length > 0) {
            featuresHtml = '<ul class="list-unstyled mb-0">';
            features.forEach(feature => {
                featuresHtml += `<li><i class="fas fa-check text-success mr-1"></i> ${feature}</li>`;
            });
            featuresHtml += '</ul>';
        }

        const previewHtml = `
            <div class="package-preview-card">
                <h6 class="font-weight-bold text-primary">${name}</h6>
                <div class="mb-2">
                    <span class="badge badge-info">${location}</span>
                </div>
                <div class="mb-2">
                    <span class="h5 text-success">${parseFloat(price).toFixed(2)}</span>
                    <small class="text-muted">/ ${duration} days</small>
                </div>
                <div class="mb-2">
                    <small class="text-muted">
                        <i class="fas fa-users mr-1"></i> ${slots} slots available
                    </small>
                </div>
                ${featuresHtml}
            </div>
        `;

        $('#packagePreview').html(previewHtml);
    }

    function resetForm() {
        if (confirm('Are you sure you want to reset the form? All unsaved changes will be lost.')) {
            document.getElementById('packageForm').reset();
            updatePreview();
            $('#locationHelp').text('');
        }
    }

    // Form validation
    $('#packageForm').on('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        const requiredFields = ['name', 'location', 'price', 'duration_days', 'max_slots'];
        requiredFields.forEach(field => {
            const input = $(`#${field}`);
            if (!input.val().trim()) {
                input.addClass('is-invalid');
                isValid = false;
            } else {
                input.removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                icon: 'error'
            });
        }
    });
</script>
@endsection