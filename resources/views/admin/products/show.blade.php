@extends('admin.layouts.admin')

@section('title', 'Product Details')

@section('breadcrumb-parent', 'Products')
@section('breadcrumb-parent-route', route('admin.products'))
@section('breadcrumb-current', 'Product Details')

@section('admin-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">Product Details: {{ $product->product_name }}</h5>
                <div>
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <!-- Product Thumbnail -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Product Thumbnail</h6>
                            </div>
                            <div class="card-body text-center">
                                <img src="{{ asset('uploads/products/thumbnails/' . $product->product_thumbnail) }}"
                                     alt="{{ $product->product_name }}"
                                     class="img-fluid" style="max-height: 300px;">
                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="col-md-8 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Product Name</th>
                                        <td>{{ $product->product_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Product Code</th>
                                        <td>{{ $product->product_code }}</td>
                                    </tr>
                                    <tr>
                                        <th>Brand</th>
                                        <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Subcategory</th>
                                        <td>{{ $product->subcategory->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Regular Price</th>
                                        <td>${{ number_format($product->selling_price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Discount Price</th>
                                        <td>{{ $product->discount_price ? '$'.number_format($product->discount_price, 2) : 'No discount' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Quantity</th>
                                        <td>{{ $product->product_qty }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($product->status)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Product Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <h6>Tags</h6>
                                        <p>{{ $product->product_tags ?: 'No tags' }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <h6>Available Sizes</h6>
                                        <p>{{ $product->product_size ?: 'No sizes specified' }}</p>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <h6>Available Colors</h6>
                                        <p>{{ $product->product_color ?: 'No colors specified' }}</p>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <h6>Special Options</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @if($product->hot_deals)
                                                <span class="badge bg-danger">Hot Deals</span>
                                            @endif
                                            @if($product->featured)
                                                <span class="badge bg-info">Featured</span>
                                            @endif
                                            @if($product->special_offer)
                                                <span class="badge bg-warning">Special Offer</span>
                                            @endif
                                            @if($product->special_deals)
                                                <span class="badge bg-success">Special Deals</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <h6>Short Description</h6>
                                        <div>{!! $product->short_description !!}</div>
                                    </div>

                                    <div class="col-md-12">
                                        <h6>Long Description</h6>
                                        <div>{!! $product->long_description !!}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
{{-- @dd($product) --}}
                    <!-- Additional Images -->
                    @if($product->productMultiImages->count() > 0)
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title">Additional Images</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($product->productMultiImages as $image)
                                    <div class="col-md-3 mb-3">
                                        <div class="position-relative">
                                            <img src="{{ asset('uploads/products/multi-images/' . $image->photo_name) }}"
                                                 alt="Product Image"
                                                 class="img-fluid rounded" style="height: 200px; width: 100%; object-fit: cover;">
                                            <form action="{{ route('admin.product.delete.multi-image', $image->id) }}" method="POST" class="position-absolute" style="top: 5px; right: 5px;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this image?');">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
