@extends('vendor.layouts.vendor')

@section('title', 'My Products')

@section('css')

@endsection

@section('vendor')
    <div class="container-fluid" style="height: 100vh">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">My Products</h4>
                    <div class="page-title-right">
                        <a href="{{ route('vendor.products.create') }}" class="btn btn-primary waves-effect waves-light">
                            <i class="fas fa-plus me-1"></i> Add New Product
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped dt-responsive nowrap w-100" id="datatables">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <img src="{{ asset('uploads/products/thumbnails/' . $product->product_thumbnail) }}"
                                                    alt="{{ $product->product_name }}" class="img-thumbnail" width="70">
                                            </td>
                                            <td>{{ $product->product_name }}</td>
                                            <td>{{ $product->product_code }}</td>
                                            <td>{{ $product->category->name }}</td>
                                            <td>
                                                @if ($product->discount_price)
                                                    <span
                                                        class="text-danger">{{ $product->getFormattedPriceWithCurrency() }}</span>
                                                    <s
                                                        class="text-muted">{{ session('currency_symbol', '₦') }}{{ number_format($product->selling_price, 2) }}</s>
                                                @else
                                                    {{ $product->getFormattedPriceWithCurrency() }}
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-check form-switch form-switch-lg" dir="ltr">
                                                    <input type="checkbox" class="form-check-input product-status"
                                                        id="product-status-{{ $product->id }}"
                                                        data-product-id="{{ $product->id }}"
                                                        {{ $product->status ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('vendor.products.show', $product->id) }}"
                                                        class="btn btn-info btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('vendor.products.edit', $product->id) }}"
                                                        class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm delete-product"
                                                        data-product-id="{{ $product->id }}" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Product Modal -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteProductForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function() {

        // Product status toggle
        $('.product-status').on('change', function() {
            const productId = $(this).data('product-id');
            const status = $(this).prop('checked') ? 1 : 0;
            const toggleSwitch = $(this);

            $.ajax({
                url: `{{ url('vendor/products') }}/${productId}/toggle-status`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error('Error updating status');
                        toggleSwitch.prop('checked', !status);
                    }
                },
                error: function() {
                    toastr.error('Error updating status');
                    // Revert toggle if there was an error
                    toggleSwitch.prop('checked', !status);
                }
            });
        });

        // Delete product button handler
        $('.delete-product').on('click', function() {
            const productId = $(this).data('product-id');
            $('#deleteProductForm').attr('action', `{{ url('vendor/products/delete') }}/${productId}`);

            // Show the modal using Bootstrap 5 syntax
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteProductModal'));
            deleteModal.show();
        });
    });
</script>
@endsection
