@extends('vendor.layouts.vendor')

@section('title', 'Dashboard')

@section('vendor')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Add New Product</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('vendor.products') }}">Products</a></li>
                            <li class="breadcrumb-item active">Add New Product</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form id="productForm" method="POST" action="{{ route('vendor.products.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Basic Information</h4>

                            <div class="mb-3">
                                <label for="product_name" class="form-label">Product Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="product_name" name="product_name"
                                    value="{{ old('product_name') }}" required>
                                @error('product_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="brand_id" class="form-label">Brand</label>
                                        <select class="form-select select2" id="brand_id" name="brand_id">
                                            <option value="">Select Brand</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('brand_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select select2" id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>

                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="subcategory_id" class="form-label">Subcategory <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select select2" id="subcategory_id" name="subcategory_id"
                                            required>
                                            <option value="">Select Category First</option>
                                        </select>
                                        @error('subcategory_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="product_tags" class="form-label">Product Tags</label>
                                        <input type="text" class="form-control" id="product_tags" name="product_tags"
                                            value="{{ old('product_tags') }}" data-role="tagsinput">
                                        <small class="text-muted">Separate tags with commas</small>
                                        @error('product_tags')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="product_qty" class="form-label">Quantity <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="product_qty" name="product_qty"
                                            min="0" step="1" value="{{ old('product_qty', 1) }}" required>
                                        @error('product_qty')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="product_size" class="form-label">Available Sizes</label>
                                        <input type="text" class="form-control" id="product_size" name="product_size"
                                            value="{{ old('product_size') }}" data-role="tagsinput">
                                        <small class="text-muted">Example: Small,Medium,Large</small>
                                        @error('product_size')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="product_color" class="form-label">Available Colors</label>
                                        <input type="text" class="form-control" id="product_color"
                                            name="product_color" value="{{ old('product_color') }}"
                                            data-role="tagsinput">
                                        <small class="text-muted">Example: Red,Blue,Green</small>
                                        @error('product_color')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Pricing</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="selling_price" class="form-label">Regular Price
                                            ({{ $currencySymbol }}) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="selling_price"
                                            name="selling_price" min="0" step="0.01"
                                            value="{{ old('selling_price') }}" required>
                                        <small class="text-muted">Price in {{ $currency }}</small>
                                        @error('selling_price')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="discount_price" class="form-label">Discount Price
                                            ({{ $currencySymbol }})</label>
                                        <input type="number" class="form-control" id="discount_price"
                                            name="discount_price" min="0" step="0.01"
                                            value="{{ old('discount_price') }}">
                                        <small class="text-muted">Leave empty for no discount</small>
                                        @error('discount_price')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Product Description</h4>

                            <div class="mb-3">
                                <label for="short_description" class="form-label">Short Description <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="short_description" name="short_description" rows="3" required>{{ old('short_description') }}</textarea>
                                @error('short_description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="long_description" class="form-label">Long Description <span
                                        class="text-danger">*</span></label>
                                <div id="editor-container">
                                    <div id="long_description_editor" style="height: 300px;">{!! old('long_description') !!}</div>
                                    <input type="hidden" name="long_description" id="long_description_input">
                                </div>
                                @error('long_description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Measurement Information</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="measurement_unit_id" class="form-label">Measurement Unit</label>
                                        <select class="form-select select2" id="measurement_unit_id"
                                            name="measurement_unit_id">
                                            <option value="">Select Measurement Unit</option>
                                            @foreach ($measurementUnits as $unit)
                                                <option value="{{ $unit->id }}"
                                                    {{ old('measurement_unit_id') == $unit->id ? 'selected' : '' }}
                                                    data-is-weight="{{ $unit->is_weight ? 'true' : 'false' }}">
                                                    {{ $unit->name }} ({{ $unit->symbol }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('measurement_unit_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="base_unit" class="form-label">Base Unit</label>
                                        <input type="text" class="form-control" id="base_unit" name="base_unit"
                                            value="{{ old('base_unit') }}">
                                        <small class="text-muted">E.g., gram for weight, piece for items</small>
                                        @error('base_unit')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="is_weight_based" name="is_weight_based"
                                                {{ old('is_weight_based') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_weight_based">Weight-based
                                                Pricing</label>
                                        </div>
                                        <small class="text-muted">Enable for products sold by weight</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="allow_decimal_qty" name="allow_decimal_qty"
                                                {{ old('allow_decimal_qty') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="allow_decimal_qty">Allow Decimal
                                                Quantities</label>
                                        </div>
                                        <small class="text-muted">E.g., 1.5kg or 0.5 meters</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="conversion_factor" class="form-label">Conversion Factor</label>
                                        <input type="number" class="form-control" id="conversion_factor"
                                            name="conversion_factor" min="0.001" step="0.00001"
                                            value="{{ old('conversion_factor', 1) }}">
                                        <small class="text-muted">For conversion between units</small>
                                        @error('conversion_factor')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="min_order_qty" class="form-label">Minimum Order Quantity</label>
                                        <input type="number" class="form-control" id="min_order_qty"
                                            name="min_order_qty" min="0.01" step="0.01"
                                            value="{{ old('min_order_qty', 1) }}">
                                        @error('min_order_qty')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="max_order_qty" class="form-label">Maximum Order Quantity</label>
                                        <input type="number" class="form-control" id="max_order_qty"
                                            name="max_order_qty" min="0" step="0.01"
                                            value="{{ old('max_order_qty') }}">
                                        <small class="text-muted">Leave empty for no limit</small>
                                        @error('max_order_qty')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Inventory Management</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" value="1" id="track_inventory"
                                                name="track_inventory"
                                                {{ old('track_inventory', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="track_inventory">Track Inventory</label>
                                        </div>
                                        <small class="text-muted">Enable to manage stock levels</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" value="1" id="allow_backorders"
                                                name="allow_backorders" {{ old('allow_backorders') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="allow_backorders">Allow
                                                Backorders</label>
                                        </div>
                                        <small class="text-muted">Allow orders when out of stock</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="low_stock_threshold" class="form-label">Low Stock Threshold</label>
                                        <input type="number" class="form-control" id="low_stock_threshold"
                                            name="low_stock_threshold" min="0" step="1"
                                            value="{{ old('low_stock_threshold') }}">
                                        <small class="text-muted">Generate alerts when stock falls below this level</small>
                                        @error('low_stock_threshold')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" value="1" type="checkbox" id="enable_stock_alerts"
                                                name="enable_stock_alerts"
                                                {{ old('enable_stock_alerts', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_stock_alerts">Enable Stock
                                                Alerts</label>
                                        </div>
                                        <small class="text-muted">Receive notifications for low stock</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Product Status</h4>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" value="1" id="status"
                                    name="status" {{ old('status', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">Active</label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line align-middle me-1"></i> Save Product
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Product Features</h4>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" value="1" id="featured"
                                    name="featured" {{ old('featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="featured">Featured Product</label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" value="1" id="hot_deals"
                                    name="hot_deals" {{ old('hot_deals') ? 'checked' : '' }}>
                                <label class="form-check-label" for="hot_deals">Hot Deal</label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" value="1" id="special_offer"
                                    name="special_offer" {{ old('special_offer') ? 'checked' : '' }}>
                                <label class="form-check-label" for="special_offer">Special Offer</label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" value="1" id="special_deals"
                                    name="special_deals" {{ old('special_deals') ? 'checked' : '' }}>
                                <label class="form-check-label" for="special_deals">Special Deal</label>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Product Thumbnail</h4>

                            <div class="mb-3">
                                <label for="product_thumbnail" class="form-label">Main Image <span
                                        class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="product_thumbnail"
                                    name="product_thumbnail" accept="image/*" required>
                                <small class="text-muted">Recommended size: 800x800px</small>
                                @error('product_thumbnail')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="thumbnail-preview mt-2">
                                    <img id="thumbnail_preview" src="{{ asset('') }}" alt="Thumbnail Preview"
                                        class="img-fluid img-thumbnail">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Gallery Images</h4>

                            <div class="mb-3">
                                <label for="multi_images" class="form-label">Additional Images</label>
                                <input type="file" class="form-control" id="multi_images" name="multi_images[]"
                                    multiple accept="image/*">
                                <small class="text-muted">Up to 5 additional product images</small>
                                @error('multi_images')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @error('multi_images.*')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="gallery_preview" class="row mt-3">
                                <!-- Preview images will be displayed here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.css" rel="stylesheet" type="text/css" />

    <style>
        /* Make Select2 match Bootstrap form-control styling */
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            height: auto;
            min-height: 38px;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .select2-container--default .select2-selection--single {

            height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 5px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            padding-left: 0;
            color: #495057;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #6c757d;
            border: 1px solid #565e64;
            border-radius: 4px;
            margin-right: 5px;
            margin-top: 5px;

            color: #fff;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
            margin-right: 5px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #dc3545;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .select2-dropdown {
            border-color: #ced4da;
        }

        .select2-search--dropdown .select2-search__field {
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-results__option--highlighted[aria-selected] {
            background-color: #0d6efd;
        }
    </style>
@endsection


@section('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 with Bootstrap theme
            $('.select2').select2({
                theme: 'default',
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || 'Select an option';
                }
            });

            // Convert tag inputs to Select2 multiple
            initializeTagsAsSelect2();

            // Initialize Quill editor
            var quill = new Quill('#long_description_editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{
                            'header': [1, 2, 3, 4, 5, 6, false]
                        }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'script': 'sub'
                        }, {
                            'script': 'super'
                        }],
                        [{
                            'indent': '-1'
                        }, {
                            'indent': '+1'
                        }],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
            });

            // Update hidden input with Quill content on form submit
            $('#productForm').on('submit', function() {
                $('#long_description_input').val(quill.root.innerHTML);
            });

            // Load subcategories when a category is selected
            $('#category_id').on('change', function() {
                var categoryId = $(this).val();

                if (categoryId) {
                    $.ajax({
                        url: '{{ route('vendor.get.getSubcategories') }}',
                        type: 'GET',
                        data: {
                            category_id: categoryId
                        },
                        success: function(data) {
                            $('#subcategory_id').empty();
                            $('#subcategory_id').append(
                                '<option value="">Select Subcategory</option>');

                            $.each(data, function(key, value) {
                                $('#subcategory_id').append(
                                    '<option value="' + value.id + '">' + value
                                    .name + '</option>');
                            });

                            // Refresh Select2 after updating options
                            $('#subcategory_id').trigger('change');
                        }
                    });
                } else {
                    $('#subcategory_id').empty();
                    $('#subcategory_id').append('<option value="">Select Category First</option>');
                    $('#subcategory_id').trigger('change');
                }
            });

            // Update measurement unit details when unit changes
            $('#measurement_unit_id').on('change', function() {
                var unitId = $(this).val();

                if (unitId) {
                    // Get unit details from the server
                    $.ajax({
                        url: '{{ route('vendor.measurement-units.get-unit-details') }}',
                        type: 'GET',
                        data: {
                            unit_id: unitId
                        },
                        success: function(data) {
                            // Update base unit field
                            if (data.base_unit && data.base_unit.name) {
                                $('#base_unit').val(data.base_unit.name);
                            } else {
                                $('#base_unit').val(data.name);
                            }

                            // Set weight-based checkbox based on unit type
                            $('#is_weight_based').prop('checked', data.is_weight);

                            // Auto-enable decimal quantities for weight-based products
                            if (data.is_weight) {
                                $('#allow_decimal_qty').prop('checked', true);
                            }

                            // Set conversion factor if available
                            if (data.conversion_factor) {
                                $('#conversion_factor').val(data.conversion_factor);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching unit details:", error);
                        }
                    });
                } else {
                    // Clear the base unit field if no unit is selected
                    $('#base_unit').val('');
                }
            });

            // Auto-enable decimal quantities for weight-based products
            $('#is_weight_based').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#allow_decimal_qty').prop('checked', true);
                }
            });

            // Image preview for thumbnail
            $('#product_thumbnail').on('change', function() {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#thumbnail_preview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Image preview for gallery
            $('#multi_images').on('change', function() {
                $('#gallery_preview').html('');
                var files = this.files;

                for (var i = 0; i < Math.min(files.length, 5); i++) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#gallery_preview').append(`
                            <div class="col-4 mb-2">
                                <img src="${e.target.result}" class="img-fluid img-thumbnail" alt="Gallery Image">
                            </div>
                        `);
                    }
                    reader.readAsDataURL(files[i]);
                }
            });
        });

        // Function to replace bootstrap-tagsinput with Select2 multiple
        function initializeTagsAsSelect2() {
            // Convert product tags input to Select2
            convertTagInputToSelect2('product_tags', 'Product Tags');

            // Convert size tags input to Select2
            convertTagInputToSelect2('product_size', 'Available Sizes');

            // Convert color tags input to Select2
            convertTagInputToSelect2('product_color', 'Available Colors');
        }

        function convertTagInputToSelect2(elementId, placeholder) {
            // Get the original input element
            var originalInput = $('#' + elementId);

            // Get existing value and split by comma if any
            var existingValues = originalInput.val() ? originalInput.val().split(',') : [];

            // Create a new select element with multiple attribute
            var selectHtml = '<select class="form-control tags-select2" id="' + elementId +
                '_select" multiple="multiple" data-placeholder="' + placeholder + '"></select>';

            // Replace the original input with the new select
            originalInput.after(selectHtml);

            // Hide original input but keep it for form submission
            originalInput.attr('type', 'hidden');

            // Initialize Select2 on the new select
            var select2Element = $('#' + elementId + '_select').select2({
                tags: true,
                tokenSeparators: [','],
                placeholder: placeholder,
                width: '100%',
                theme: 'default'
            });

            // Add existing values as options
            if (existingValues.length > 0 && existingValues[0] !== '') {
                $.each(existingValues, function(i, tag) {
                    var option = new Option(tag.trim(), tag.trim(), true, true);
                    select2Element.append(option);
                });
                select2Element.trigger('change');
            }

            // Update hidden input when Select2 changes
            select2Element.on('change', function() {
                var values = $(this).val() || [];
                originalInput.val(values.join(','));
            });
        }
    </script>
@endsection
