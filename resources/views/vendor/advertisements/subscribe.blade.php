@extends('vendor.layouts.vendor')

@section('title', isset($package) ? 'Subscribe to ' . $package->name : 'Advertisement Subscription')

@section('vendor')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h3 mb-0">
                        <i class="fas fa-bullhorn text-primary me-2"></i>
                        Advertisement Subscription
                    </h2>
                    <a href="{{ route('vendor.advertisements.packages') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Packages
                    </a>
                </div>
                <form id="subscriptionForm" action="{{ route('vendor.advertisements.process-subscription') }}" method="POST"
                    enctype="multipart/form-data">
                    <!-- Package Selector -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="package-selector">
                                <h4 class="mb-3">
                                    <i class="fas fa-box-open me-2"></i>
                                    Select Advertisement Package
                                </h4>
                                <select id="packageSelector" class="form-select form-select-lg">
                                    <option value="">Choose a package to get started...</option>
                                    @foreach ($packages ?? [] as $pkg)
                                        <option {{ old('package_id') == $pkg->id ? 'selected' : '' }}
                                            value="{{ $pkg->id }}" data-package="{{ json_encode($pkg) }}">
                                            {{ $pkg->name }} - {{ $pkg->location_display }}
                                            (₦{{ number_format($pkg->price, 2) }} for {{ $pkg->duration_days }} days)
                                        </option>
                                    @endforeach
                                </select>
                                <small><i class="fas fa-info-circle me-2"></i>Note: This package will be used for your
                                    advertisement.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="package-selector">
                                <h4 class="mb-3">
                                    <i class="fas fa-box-open me-2"></i>
                                    Select Product
                                </h4>
                                <select id="product_id" name="product_id" class="form-select form-select-lg">
                                    <option value="">Choose a product to get started...</option>
                                    @foreach ($products ?? [] as $product)
                                        <option {{ old('product_id') == $product->id ? 'selected' : '' }}
                                            value="{{ $product->id }}" data-product="{{ json_encode($product) }}">
                                            {{ $product->product_name }} (₦{{ number_format($product->selling_price, 2) }}
                                            )
                                        </option>
                                    @endforeach
                                </select>
                                <small><i class="fas fa-info-circle me-2"></i>Note: It is optional to select a
                                    product.</small>
                            </div>
                        </div>
                    </div>


                    <!-- Alert Messages -->
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-circle me-2"></i>Please fix the following errors:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Package Details Section -->
                    <div id="packageDetailsSection">
                        <div class="row mb-4">
                            <div class="col-lg-6 mb-4">
                                <div class="card package-details-card h-100">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                            Package Details
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <strong>Location:</strong>
                                                <div id="packageLocation" class="text-muted">
                                                    {{ $package->location_display ?? 'Not selected' }}
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <strong>Duration:</strong>
                                                <div id="packageDuration" class="text-muted">
                                                    {{ isset($package) ? $package->duration_days . ' days' : 'Not selected' }}
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <strong>Price:</strong>
                                                <div id="packagePrice" class="text-success h5">
                                                    ₦{{ isset($package) ? number_format($package->price, 2) : '0.00' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-4">
                                <div class="card benefits-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-star me-2"></i>
                                            What You Get
                                        </h5>
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check me-2"></i>Professional advertisement display</li>
                                            <li><i class="fas fa-check me-2"></i>Prime location visibility</li>
                                            <li><i class="fas fa-check me-2"></i>Click and impression tracking</li>
                                            <li><i class="fas fa-check me-2"></i>Performance analytics dashboard</li>
                                            <li><i class="fas fa-check me-2"></i>Auto-renewal option available</li>
                                            <li><i class="fas fa-check me-2"></i>24/7 customer support</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image Requirements Alert -->
                        <div id="imageRequirements" class="image-requirements"
                            style="display: {{ isset($package) ? 'block' : 'none' }}">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-image text-warning me-3 fs-4"></i>
                                <div>
                                    <h6 class="mb-1">Image Requirements</h6>
                                    <div id="requirementDetails">
                                        @if (isset($specifications[$package->location]))
                                            <strong>Size:</strong>
                                            {{ $specifications[$package->location]['recommended_size'] ?? 'N/A' }} <br>
                                            <strong>Max File Size:</strong>
                                            {{ $specifications[$package->location]['max_file_size'] ?? 0 }}KB <br>
                                            <strong>Formats:</strong> JPG, PNG, GIF <br>
                                            <strong>Width:</strong>
                                            {{ $specifications[$package->location]['width'] ?? 'N/A' }}
                                            px <br>
                                            <strong>Height:</strong>
                                            {{ $specifications[$package->location]['height'] ?? 'N/A' }} px
                                        @else
                                            <strong>Size:</strong> N/A |
                                            <strong>Max File Size:</strong> 0KB |
                                            <strong>Formats:</strong> None
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Subscription Form -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-edit text-primary me-2"></i>
                                    Advertisement Details
                                </h5>
                            </div>
                            <div class="card-body">

                                @csrf
                                <input type="hidden" id="packageIdInput" name="package_id"
                                    value="{{ $package->id ?? '' }}">
                                <input type="hidden" id="croppedImageData" name="cropped_image">

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">
                                                <i class="fas fa-heading me-1"></i>
                                                Advertisement Title <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                                id="title" name="title" value="{{ old('title') }}"
                                                placeholder="Enter a catchy title for your advertisement" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="link_url" class="form-label">
                                                <i class="fas fa-link me-1"></i>
                                                Target URL <span class="text-danger">*</span>
                                            </label>
                                            <input type="url"
                                                class="form-control @error('link_url') is-invalid @enderror"
                                                id="link_url" name="link_url" value="{{ old('link_url') }}"
                                                placeholder="https://your-website.com" required>
                                            <div class="form-text">URL where users will be redirected when they click your
                                                ad</div>
                                            @error('link_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left me-1"></i>
                                        Description (Optional)
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                        rows="3" placeholder="Brief description of your advertisement">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Image Upload Section -->
                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-image me-1"></i>
                                        Advertisement Image <span class="text-danger">*</span>
                                    </label>

                                    <!-- Image Upload Container -->
                                    <div id="imageUploadContainer" class="image-upload-container">
                                        <input type="file" id="imageInput" name="image" accept="image/*"
                                            style="display: none;" required>
                                        <div id="uploadPrompt">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <h5>Drop your image here or click to browse</h5>
                                            <p class="text-muted">Supports: JPG, PNG, GIF (Max: 5MB)</p>
                                            <button type="button" class="btn btn-outline-primary"
                                                onclick="document.getElementById('imageInput').click()">
                                                <i class="fas fa-folder-open me-1"></i>
                                                Choose File
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Image Preview and Cropping -->
                                    <div id="imagePreviewContainer" class="image-preview-container">
                                        <img id="imagePreview" src="" alt="Preview">
                                    </div>

                                    <!-- Crop Controls -->
                                    <div id="cropControls" class="crop-controls">
                                        <button type="button" id="cropButton" class="btn btn-crop me-2">
                                            <i class="fas fa-crop me-1"></i>
                                            Crop Image
                                        </button>
                                        <button type="button" id="resetCrop" class="btn btn-outline-secondary me-2">
                                            <i class="fas fa-undo me-1"></i>
                                            Reset
                                        </button>
                                        <button type="button" id="cancelCrop" class="btn btn-outline-danger">
                                            <i class="fas fa-times me-1"></i>
                                            Cancel
                                        </button>
                                    </div>

                                    <!-- Final Preview -->
                                    <div id="finalPreview" class="final-preview">
                                        <h6>Final Preview:</h6>
                                        <img id="finalPreviewImage" src="" alt="Final Preview">
                                        <div class="mt-2">
                                            <button type="button" id="editAgain" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit me-1"></i>
                                                Edit Again
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Upload Progress -->
                                    <div id="uploadProgress" class="upload-progress">
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>

                                    @error('image')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Auto-renewal Option -->
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="auto_renew"
                                            name="auto_renew" value="1" {{ old('auto_renew') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="auto_renew">
                                            <i class="fas fa-sync-alt me-1"></i>
                                            Enable Auto-Renewal
                                        </label>
                                    </div>
                                    <div class="form-text">
                                        Automatically renew this advertisement when it expires (you'll be notified before
                                        renewal)
                                    </div>
                                </div>

                                <hr>

                                <!-- Payment Section -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">
                                            Total Amount:
                                            <span id="totalAmount" class="text-success">
                                                ₦{{ isset($package) ? number_format($package->price, 2) : '0.00' }}
                                            </span>
                                        </h5>
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            Secure payment powered by Paystack
                                        </small>
                                    </div>
                                    <button type="submit" id="paymentButton" class="btn btn-payment" disabled>
                                        <span class="loading-spinner">
                                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                            Processing...
                                        </span>
                                        <span class="payment-text">
                                            <i class="fas fa-credit-card me-2"></i>
                                            Proceed to Payment
                                        </span>
                                    </button>
                                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>

@endsection


@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <style>
        .package-selector {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .package-details-card {
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .package-details-card:hover {
            transform: translateY(-5px);
        }

        .benefits-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 15px;
        }

        .image-upload-container {
            border: 2px dashed #ddd;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            background: #f8f9fa;
            position: relative;
            overflow: hidden;
        }

        .image-upload-container.drag-over {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }

        .image-preview-container {
            max-width: 100%;
            max-height: 400px;
            margin: 20px 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .cropper-container {
            max-height: 400px;
        }

        .image-requirements {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
            display: none;
        }

        .crop-controls {
            display: none;
            margin: 20px 0;
            text-align: center;
        }

        .btn-crop {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-crop:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .final-preview {
            display: none;
            margin: 20px 0;
            text-align: center;
        }

        .final-preview img {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            height: auto;
        }

        .upload-progress {
            display: none;
            margin: 15px 0;
        }

        .package-info-alert {
            background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
            color: white;
            border: none;
            border-radius: 15px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-payment {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-payment:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .loading-spinner {
            display: none;
        }

        .package-selector .form-select {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 10px;
        }

        .package-selector .form-select option {
            background: #333;
            color: white;
        }

        .package-selector .form-select:focus {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
        }
    </style>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cropper = null;
            let currentPackage = @json($package ?? null);
            let packages = @json($packages ?? []);
            let specifications = @json($specifications ?? []);


            // Initialize with existing package if available
            if (currentPackage) {
                updatePackageInfo(currentPackage);
                document.getElementById('paymentButton').disabled = false;
            }

            // Package selector change event
            const packageSelector = document.getElementById('packageSelector');
            if (packageSelector) {
                packageSelector.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption.value) {
                        const packageData = JSON.parse(selectedOption.dataset.package);
                        currentPackage = packageData;
                        updatePackageInfo(packageData);
                        document.getElementById('packageDetailsSection').style.display = 'block';
                        document.getElementById('packageIdInput').value = packageData.id;
                        document.getElementById('paymentButton').disabled = false;
                    } else {
                        document.getElementById('packageDetailsSection').style.display = 'none';
                        document.getElementById('paymentButton').disabled = true;
                        currentPackage = null;
                    }
                });
            }

            // Update package information display
            function updatePackageInfo(package) {
                document.getElementById('packageLocation').textContent = package.location_display || package
                    .location || 'Not available';
                document.getElementById('packageDuration').textContent = package.duration_days + ' days';
                document.getElementById('packagePrice').textContent = '₦' + numberFormat(package.price);
                document.getElementById('totalAmount').textContent = '₦' + numberFormat(package.price);

                // Update image requirements
                const requirements = document.getElementById('requirementDetails');
                if (specifications[package.location]) {
                    const spec = specifications[package.location];
                    requirements.innerHTML = `
                        <strong>Size:</strong> ${spec.recommended_size || 'N/A'} |
                        <strong>Max File Size:</strong> ${spec.max_file_size || 0}KB |
                        <strong>Formats:</strong> JPG, PNG, GIF
                    `;
                    document.getElementById('imageRequirements').style.display = 'block';
                } else {
                    requirements.innerHTML = `
                        <strong>Size:</strong> N/A |
                        <strong>Max File Size:</strong> 0KB |
                        <strong>Formats:</strong> None
                    `;
                    document.getElementById('imageRequirements').style.display = 'none';
                }
            }

            // Number formatting helper
            function numberFormat(num) {
                return parseFloat(num).toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            // Image upload handling
            const imageInput = document.getElementById('imageInput');
            const imageUploadContainer = document.getElementById('imageUploadContainer');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const imagePreview = document.getElementById('imagePreview');
            const cropControls = document.getElementById('cropControls');
            const finalPreview = document.getElementById('finalPreview');
            const finalPreviewImage = document.getElementById('finalPreviewImage');

            // Drag and drop functionality
            imageUploadContainer.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });

            imageUploadContainer.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
            });

            imageUploadContainer.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    handleImageFile(files[0]);
                }
            });

            // Click to upload
            imageUploadContainer.addEventListener('click', function() {
                imageInput.click();
            });

            // File input change
            imageInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    handleImageFile(e.target.files[0]);
                }
            });

            // Handle image file
            function handleImageFile(file) {
                if (!currentPackage) {
                    alert('Please select a package first!');
                    return;
                }

                // Validate file type
                if (!file.type.match('image.*')) {
                    alert('Please select a valid image file.');
                    return;
                }

                // Validate file size
                const maxSize = (specifications[currentPackage.location]?.max_file_size || 2048) *
                    1024; // Convert KB to bytes
                if (file.size > maxSize) {
                    alert(
                        `File size must not exceed ${specifications[currentPackage.location]?.max_file_size || 2048}KB`
                    );
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imageUploadContainer.style.display = 'none';
                    imagePreviewContainer.style.display = 'block';
                    cropControls.style.display = 'block';
                    finalPreview.style.display = 'none';

                    // Initialize cropper
                    if (cropper) {
                        cropper.destroy();
                    }

                    const aspectRatio = getAspectRatio();
                    cropper = new Cropper(imagePreview, {
                        aspectRatio: aspectRatio,
                        viewMode: 1,
                        guides: true,
                        center: true,
                        highlight: true,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                };
                reader.readAsDataURL(file);
            }

            // Get aspect ratio from package specifications
            function getAspectRatio() {
                if (currentPackage && specifications[currentPackage.location] && specifications[currentPackage
                        .location].dimensions) {
                    const {
                        width,
                        height
                    } = specifications[currentPackage.location].dimensions;
                    return width / height;
                }
                return NaN; // Free aspect ratio if no dimensions specified
            }

            // Crop button click
            document.getElementById('cropButton').addEventListener('click', function() {
                if (!cropper) return;

                const canvas = cropper.getCroppedCanvas({
                    width: currentPackage?.specifications?.dimensions?.width,
                    height: currentPackage?.specifications?.dimensions?.height,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });

                canvas.toBlob(function(blob) {
                    const url = URL.createObjectURL(blob);
                    finalPreviewImage.src = url;

                    // Convert to base64 for form submission
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('croppedImageData').value = e.target.result;
                    };
                    reader.readAsDataURL(blob);

                    // Show final preview
                    imagePreviewContainer.style.display = 'none';
                    cropControls.style.display = 'none';
                    finalPreview.style.display = 'block';

                    // Enable form submission
                    document.getElementById('paymentButton').disabled = false;
                }, 'image/jpeg', 0.9);
            });

            // Reset crop
            document.getElementById('resetCrop').addEventListener('click', function() {
                if (cropper) {
                    cropper.reset();
                }
            });

            // Cancel crop
            document.getElementById('cancelCrop').addEventListener('click', function() {
                cancelImageUpload();
            });

            // Edit again
            document.getElementById('editAgain').addEventListener('click', function() {
                finalPreview.style.display = 'none';
                imagePreviewContainer.style.display = 'block';
                cropControls.style.display = 'block';
            });

            // Cancel image upload
            function cancelImageUpload() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                imageInput.value = '';
                document.getElementById('croppedImageData').value = '';
                imageUploadContainer.style.display = 'block';
                imagePreviewContainer.style.display = 'none';
                cropControls.style.display = 'none';
                finalPreview.style.display = 'none';
            }

            // Form submission handling
            document.getElementById('subscriptionForm').addEventListener('submit', function(e) {
                if (!document.getElementById('croppedImageData').value && !imageInput.files.length) {
                    e.preventDefault();
                    alert('Please upload and crop an image for your advertisement.');
                    return;
                }

                // Show loading state
                const button = document.getElementById('paymentButton');
                button.disabled = true;
                button.querySelector('.loading-spinner').style.display = 'inline-block';
                button.querySelector('.payment-text').style.display = 'none';
            });

            // Prevent form submission on Enter key in input fields (except textarea)
            document.querySelectorAll('input[type="text"], input[type="url"], input[type="email"]').forEach(
                input => {
                    input.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                        }
                    });
                });
        });
    </script>
@endsection
