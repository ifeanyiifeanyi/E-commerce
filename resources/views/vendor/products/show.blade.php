@extends('vendor.layouts.vendor')

@section('title', 'Product Details')

@section('vendor')
    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between mx-3 mb-4">
                    <h4 class="mb-0"></h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('vendor.products') }}">Products</a></li>
                            <li class="breadcrumb-item active">Product Details</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- Product Header Section -->
        <div class="card detail-card mb-4">
            <div class="card-header detail-header py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="m-0 font-weight-bold">{{ $product->product_name }}</h4>
                    <div class="text-muted small mt-3">
                        Code: <span class="badge bg-secondary">{{ $product->product_code }}</span>
                        @if ($product->status)
                            <span class="badge bg-success ms-2">Active</span>
                        @else
                            <span class="badge bg-danger ms-2">Inactive</span>
                        @endif

                        <!-- Stock Status -->
                        @if ($product->track_inventory)
                            @if ($product->isOutOfStock())
                                <span class="badge bg-danger ms-2">
                                    <i class="fas fa-times-circle me-1"></i> Out of Stock
                                </span>
                            @elseif($product->isLowStock())
                                <span class="badge bg-warning ms-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Low Stock
                                </span>
                            @else
                                <span class="badge bg-success ms-2">
                                    <i class="fas fa-check-circle me-1"></i> In Stock
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Images -->
            <div class="col-lg-4">
                <!-- Main Image -->
                <div class="card detail-card mb-4">
                    <div class="card-header detail-header">
                        <h5 class="card-title mb-0"><i class="fas fa-image me-2"></i>Product Image</h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="image-container">
                            @if ($product->discount_price)
                                <div class="discount-badge">
                                    <span class="badge bg-danger">{{ $product->getDiscountPercentageAttribute() }}%
                                        OFF</span>
                                </div>
                            @endif
                            <img src="{{ asset('uploads/products/thumbnails/' . $product->product_thumbnail) }}"
                                alt="{{ $product->product_name }}" class="img-fluid rounded"
                                style="width: 100%; height: auto; object-fit: contain;">
                        </div>
                    </div>
                </div>

                <!-- Pricing Information Card -->
                <div class="card detail-card mb-4">
                    <div class="card-header detail-header">
                        <h5 class="card-title mb-0"><i class="fas fa-tag me-2"></i>Pricing Information</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <!-- Display prices in both currencies -->
                            <li class="list-group-item">
                                <span class="property-label">Default Currency:</span>
                                <span class="badge bg-info">{{ $product->default_currency ?? 'NGN' }}</span>
                            </li>

                            <!-- NGN Pricing -->
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="property-label">Regular Price (NGN):</span>
                                <span>₦{{ number_format($product->selling_price, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="property-label">Discount Price (NGN):</span>
                                <span>{{ $product->discount_price ? '₦' . number_format($product->discount_price, 2) : 'No discount' }}</span>
                            </li>

                            <!-- USD Pricing -->
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="property-label">Regular Price (USD):</span>
                                <span>${{ number_format($product->selling_price_usd ?? 0, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="property-label">Discount Price (USD):</span>
                                <span>{{ $product->discount_price_usd ? '$' . number_format($product->discount_price_usd, 2) : 'No discount' }}</span>
                            </li>

                            <!-- Price with Units -->
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="property-label">Price Per Unit:</span>
                                <span>{{ $product->getFormattedPriceWithCurrency() }}</span>
                            </li>

                            @if ($product->discount_price)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="property-label">Discount:</span>
                                    <span class="badge bg-success">{{ $product->getDiscountPercentageAttribute() }}%
                                        OFF</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Inventory Information Card -->
                <div class="card detail-card mb-4">
                    <div class="card-header detail-header">
                        <h5 class="card-title mb-0"><i class="fas fa-box me-2"></i>Inventory</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="property-label">Current Stock:</span>
                                <span>{{ $product->formattedQuantity() }}</span>
                            </li>

                            @if ($product->track_inventory)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="property-label">Available Stock:</span>
                                    <span>{{ $product->formattedQuantity($product->getAvailableQtyAttribute()) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="property-label">Reserved Stock:</span>
                                    <span>{{ $product->formattedQuantity($product->reserved_qty) }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="property-label">Stock Status:</span>
                                    <span>
                                        @if ($product->stock_status == 'in_stock')
                                            <span class="stock-indicator stock-in"></span> In Stock
                                        @elseif($product->stock_status == 'low_stock')
                                            <span class="stock-indicator stock-low"></span> Low Stock
                                        @elseif($product->stock_status == 'out_of_stock')
                                            <span class="stock-indicator stock-out"></span> Out of Stock
                                        @elseif($product->stock_status == 'backordered')
                                            <span class="stock-indicator stock-out"></span> Backordered
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $product->stock_status)) }}
                                        @endif
                                    </span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="property-label">Low Stock Threshold:</span>
                                    <span>{{ $product->low_stock_threshold ? $product->formattedQuantity($product->low_stock_threshold) : 'Not set' }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="property-label">Stock Alerts:</span>
                                    <span>{{ $product->enable_stock_alerts ? 'Enabled' : 'Disabled' }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="property-label">Allow Backorders:</span>
                                    <span>{{ $product->allow_backorders ? 'Yes' : 'No' }}</span>
                                </li>

                                @if ($product->stock_last_updated)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="property-label">Last Updated:</span>
                                        <span>{{ $product->stock_last_updated->diffForHumans() }}</span>
                                    </li>
                                @endif
                            @endif

                            @if ($product->measurementUnit)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="property-label">Measurement:</span>
                                    <span>{{ $product->measurementUnit->formatted_name }}</span>
                                </li>
                            @endif

                            @if ($product->is_weight_based)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="property-label">Weight Based:</span>
                                    <span><i class="fas fa-check-circle text-success"></i></span>
                                </li>
                            @endif

                            @if ($product->min_order_qty)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="property-label">Min Order:</span>
                                    <span>{{ $product->formattedQuantity($product->min_order_qty) }}</span>
                                </li>
                            @endif

                            @if ($product->max_order_qty)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="property-label">Max Order:</span>
                                    <span>{{ $product->formattedQuantity($product->max_order_qty) }}</span>
                                </li>
                            @endif

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="property-label">Allow Decimal:</span>
                                <span>{{ $product->allow_decimal_qty ? 'Yes' : 'No' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Column - Details -->
            <div class="col-lg-8">
                <!-- Basic Information Card -->
                <div class="card detail-card mb-4">
                    <div class="card-header detail-header">
                        <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th>Name</th>
                                                <td>{{ $product->product_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Product SKU</th>
                                                <td><code>{{ $product->product_code }}</code></td>
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
                                                <th scope="row">Created At</th>
                                                <td>{{ $product->created_at->format('d M, Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Updated At</th>
                                                <td>{{ $product->updated_at->format('d M, Y') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Special Features -->
                <div class="card detail-card mb-4">
                    <div class="card-header detail-header">
                        <h5 class="card-title mb-0"><i class="fas fa-star me-2"></i>Special Features</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3 text-center">
                                <div
                                    class="card h-100 {{ $product->hot_deals ? 'bg-danger bg-opacity-10' : 'bg-light' }}">
                                    <div class="card-body">
                                        <i
                                            class="fas fa-fire fa-2x mb-2 {{ $product->hot_deals ? 'text-danger' : 'text-muted' }}"></i>
                                        <h6>Hot Deals</h6>
                                        @if ($product->hot_deals)
                                            <span class="badge bg-danger">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="card h-100 {{ $product->featured ? 'bg-info bg-opacity-10' : 'bg-light' }}">
                                    <div class="card-body">
                                        <i
                                            class="fas fa-award fa-2x mb-2 {{ $product->featured ? 'text-info' : 'text-muted' }}"></i>
                                        <h6>Featured</h6>
                                        @if ($product->featured)
                                            <span class="badge bg-info">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div
                                    class="card h-100 {{ $product->special_offer ? 'bg-warning bg-opacity-10' : 'bg-light' }}">
                                    <div class="card-body">
                                        <i
                                            class="fas fa-gift fa-2x mb-2 {{ $product->special_offer ? 'text-warning' : 'text-muted' }}"></i>
                                        <h6>Special Offer</h6>
                                        @if ($product->special_offer)
                                            <span class="badge bg-warning">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div
                                    class="card h-100 {{ $product->special_deals ? 'bg-success bg-opacity-10' : 'bg-light' }}">
                                    <div class="card-body">
                                        <i
                                            class="fas fa-percentage fa-2x mb-2 {{ $product->special_deals ? 'text-success' : 'text-muted' }}"></i>
                                        <h6>Special Deals</h6>
                                        @if ($product->special_deals)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Attributes -->
                <div class="card detail-card mb-4">
                    <div class="card-header detail-header">
                        <h5 class="card-title mb-0"><i class="fas fa-list-alt me-2"></i>Product Attributes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <h6 class="fw-bold"><i class="fas fa-tags me-2"></i>Tags</h6>
                                    @if ($product->product_tags)
                                        @foreach (explode(',', $product->product_tags) as $tag)
                                            <span class="badge bg-secondary me-1">{{ trim($tag) }}</span>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No tags specified</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <h6 class="fw-bold"><i class="fas fa-ruler me-2"></i>Available Sizes</h6>
                                    @if ($product->product_size)
                                        @foreach (explode(',', $product->product_size) as $size)
                                            <span class="badge bg-primary me-1">{{ trim($size) }}</span>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No sizes specified</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <h6 class="fw-bold"><i class="fas fa-palette me-2"></i>Available Colors</h6>
                                    @if ($product->product_color)
                                        @foreach (explode(',', $product->product_color) as $color)
                                            <span class="badge bg-dark me-1">{{ trim($color) }}</span>
                                        @endforeach
                                    @else
                                        <p class="text-muted">No colors specified</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="card detail-card mb-4">
                    <div class="card-header detail-header">
                        <h5 class="card-title mb-0"><i class="fas fa-align-left me-2"></i>Product Description</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="fw-bold">Short Description</h6>
                            <div class="p-3 bg-light rounded">{!! $product->short_description !!}</div>
                        </div>
                        <div>
                            <h6 class="fw-bold">Long Description</h6>
                            <div class="p-3 bg-light rounded">{!! $product->long_description !!}</div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Management History -->
                @if ($product->track_inventory && $product->inventoryLogs->count() > 0)
                    <div class="card detail-card mb-4">
                        <div class="card-header detail-header">
                            <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Inventory History</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Action</th>
                                            <th>Change</th>
                                            <th>Reference</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($product->inventoryLogs()->latest()->take(5)->get() as $log)
                                            <tr>
                                                <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $log->action_type == 'add'
                                                            ? 'success'
                                                            : ($log->action_type == 'remove'
                                                                ? 'danger'
                                                                : ($log->action_type == 'reserve'
                                                                    ? 'warning'
                                                                    : 'info')) }}">
                                                        {{ ucfirst($log->action_type) }}
                                                    </span>
                                                </td>
                                                <td>{{ $log->quantity_change > 0 ? '+' : '' }}{{ $product->formattedQuantity($log->quantity_change) }}
                                                </td>
                                                <td>
                                                    @if ($log->reference_type)
                                                        {{ ucfirst($log->reference_type) }} #{{ $log->reference_id }}
                                                    @else
                                                        Manual adjustment
                                                    @endif
                                                </td>
                                                <td>{{ $log->notes ?? 'No notes' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Additional Images -->
        @if ($product->productMultiImages->count() > 0)
            <div class="row">
                <div class="col-lg-7 mx-auto">
                    <div class="card detail-card mb-4">
                        <div class="card-header detail-header">
                            <h5 class="card-title mb-0"><i class="fas fa-images me-2"></i>Additional Images</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="additional-images">
                                @foreach ($product->productMultiImages as $image)
                                    <div class="position-relative mb-3">
                                        <div class="image-container">
                                            <img src="{{ asset('uploads/products/multi-images/' . $image->photo_name) }}"
                                                alt="Product Image" class="img-fluid rounded"
                                                style="height: 200px; width: auto; object-fit: cover;">
                                        </div>
                                        <form action="{{ route('admin.product.delete.multi-image', $image->id) }}"
                                            method="POST" class="position-absolute" style="top: 5px;left: 90px;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger rounded-circle"
                                                onclick="return confirm('Are you sure you want to delete this image?');">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

@endsection


@section('css')
    <style>
        .product-badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
            margin-right: 0.5rem;
        }

        .detail-card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .detail-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .detail-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .image-container {
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
        }

        .image-container img {
            transition: transform 0.3s ease;
        }

        .image-container:hover img {
            transform: scale(1.05);
        }

        .discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10;
        }

        .action-btn {
            transition: all 0.2s;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .additional-images {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .property-label {
            font-weight: 600;
            color: #495057;
        }

        .currency-badge {
            padding: 0.25em 0.5em;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .stock-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .stock-in {
            background-color: #28a745;
        }

        .stock-low {
            background-color: #ffc107;
        }

        .stock-out {
            background-color: #dc3545;
        }
    </style>
@endsection

@section('js')
    <script>
        // Any additional JavaScript can be added here
        $(document).ready(function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
