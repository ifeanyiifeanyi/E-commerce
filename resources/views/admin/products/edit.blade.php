@extends('admin.layouts.admin')

@section('title', 'Edit Product')

@section('breadcrumb-parent', 'Products')
@section('breadcrumb-parent-route', route('admin.products'))
@section('breadcrumb-current', 'Edit Product')

@section('admin-content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Product: {{ $product->product_name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="mb-3">Basic Information</h6>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="product_name" class="form-label">Product Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('product_name') is-invalid @enderror"
                                    id="product_name" name="product_name" value="{{ old('product_name', $product->product_name) }}" required>
                                @error('product_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                            <div class="col-md-4 mb-3">
                                <label for="brand_id" class="form-label">Brand <span class="text-danger">*</span></label>
                                <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id"
                                    name="brand_id" required>
                                    <option value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ (old('brand_id', $product->brand_id) == $brand->id) ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label">Category <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="subcategory_id" class="form-label">Subcategory <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('subcategory_id') is-invalid @enderror"
                                    id="subcategory_id" name="subcategory_id" required>
                                    <option value="">Select Subcategory</option>
                                    @foreach ($subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}"
                                            {{ (old('subcategory_id', $product->subcategory_id) == $subcategory->id) ? 'selected' : '' }}>
                                            {{ $subcategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subcategory_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="mb-3">Pricing & Inventory</h6>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="selling_price" class="form-label">Regular Price <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01"
                                        class="form-control @error('selling_price') is-invalid @enderror" id="selling_price"
                                        name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required>
                                    @error('selling_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="discount_price" class="form-label">Discount Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01"
                                        class="form-control @error('discount_price') is-invalid @enderror"
                                        id="discount_price" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}">
                                    @error('discount_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="product_qty" class="form-label">Quantity <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('product_qty') is-invalid @enderror"
                                    id="product_qty" name="product_qty" value="{{ old('product_qty', $product->product_qty) }}" required>
                                @error('product_qty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="mb-3">Product Details</h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="product_tags" class="form-label">Tags</label>
                                <input type="text" class="form-control @error('product_tags') is-invalid @enderror"
                                    id="product_tags" name="product_tags" value="{{ old('product_tags', $product->product_tags) }}"
                                    placeholder="Enter tags separated by commas">
                                @error('product_tags')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="product_size" class="form-label">Available Sizes</label>
                                <input type="text" class="form-control @error('product_size') is-invalid @enderror"
                                    id="product_size" name="product_size" value="{{ old('product_size', $product->product_size) }}"
                                    placeholder="S,M,L,XL">
                                @error('product_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="product_color" class="form-label">Available Colors</label>
                                <input type="text" class="form-control @error('product_color') is-invalid @enderror"
                                    id="product_color" name="product_color" value="{{ old('product_color', $product->product_color) }}"
                                    placeholder="Red,Blue,Green">
                                @error('product_color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="short_description" class="form-label">Short Description <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description"
                                    name="short_description" rows="3" required>{{ old('short_description', $product->short_description) }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="long_description" class="form-label">Long Description <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('long_description') is-invalid @enderror" id="long_description"
                                    name="long_description" rows="6" required>{{ old('long_description', $product->long_description) }}</textarea>
                                @error('long_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="mb-3">Images</h6>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="product_thumbnail" class="form-label">Product Thumbnail</label>
                                <input type="file"
                                    class="form-control @error('product_thumbnail') is-invalid @enderror"
                                    id="product_thumbnail" name="product_thumbnail">
                                <div class="form-text">Recommended size: 800x800 pixels. Leave empty to keep current image.</div>
                                <div id="thumbnail-preview" class="mt-2">
                                    @if($product->product_thumbnail)
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('uploads/products/thumbnails/' . $product->product_thumbnail) }}"
                                                class="img-fluid" style="max-height: 150px;">
                                            <span class="ms-2">Current thumbnail</span>
                                        </div>
                                    @endif
                                </div>
                                @error('product_thumbnail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="multi_images" class="form-label">Additional Images</label>
                                <input type="file" class="form-control @error('multi_images.*') is-invalid @enderror"
                                    id="multi_images" name="multi_images[]" multiple>
                                <div class="form-text">You can select multiple images to add to the existing ones</div>
                                <div id="multi-images-preview" class="d-flex flex-wrap"></div>
                                @error('multi_images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <!-- Current Multi Images -->
                                @if($product->productMultiImages->count() > 0)
                                    <div class="mt-3">
                                        <h6>Current Additional Images</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($product->productMultiImages as $image)
                                                <div class="position-relative">
                                                    <img src="{{ asset('uploads/products/multi-images/' . $image->photo_name) }}"
                                                        alt="Product Image"
                                                        class="img-thumbnail" style="height: 100px; width: 100px; object-fit: cover;">
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="form-text">Edit individual images in the product details page</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6 class="mb-3">Product Options</h6>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="hot_deals" name="hot_deals"
                                        value="1" {{ old('hot_deals', $product->hot_deals) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="hot_deals">Hot Deals</label>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="featured" name="featured"
                                        value="1" {{ old('featured', $product->featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="featured">Featured</label>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="special_offer"
                                        name="special_offer" value="1" {{ old('special_offer', $product->special_offer) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="special_offer">Special Offer</label>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="special_deals"
                                        name="special_deals" value="1" {{ old('special_deals', $product->special_deals) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="special_deals">Special Deals</label>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="status" name="status"
                                        value="1" {{ old('status', $product->status) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize rich text editors
            $('#short_description, #long_description').summernote({
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Category change event for loading subcategories
            $('#category_id').change(function() {
                const categoryId = $(this).val();
                loadSubcategories(categoryId);
            });

            // Initial load of subcategories if category is selected
            function loadSubcategories(categoryId) {
                if (categoryId) {
                    $.ajax({
                        url: "{{ route('admin.get.subcategories') }}",
                        type: "GET",
                        data: {
                            category_id: categoryId
                        },
                        success: function(data) {
                            $('#subcategory_id').empty();
                            $('#subcategory_id').append(
                                '<option value="">Select Subcategory</option>');

                            const oldSubcategoryId = "{{ old('subcategory_id', $product->subcategory_id) }}";

                            $.each(data, function(key, value) {
                                const selected = (value.id == oldSubcategoryId) ? 'selected' : '';
                                $('#subcategory_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                        },
                        error: function() {
                            toastr.error('Error loading subcategories');
                        }
                    });
                } else {
                    $('#subcategory_id').empty();
                    $('#subcategory_id').append('<option value="">Select Subcategory</option>');
                }
            }

            // Preview for product thumbnail
            $('#product_thumbnail').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#thumbnail-preview').html('<img src="' + e.target.result +
                            '" class="img-fluid mt-2" style="max-height: 200px"> <span class="ms-2">New thumbnail</span>');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Preview for multiple images
            $('#multi_images').change(function() {
                $('#multi-images-preview').html('<h6 class="mt-2">New images to add:</h6>');
                for (let i = 0; i < this.files.length; i++) {
                    const file = this.files[i];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#multi-images-preview').append('<img src="' + e.target.result +
                                '" class="img-thumbnail mt-2 me-2" style="height: 100px; width: 100px; object-fit: cover;">');
                        }
                        reader.readAsDataURL(file);
                    }
                }
            });
        });
    </script>
@endsection
