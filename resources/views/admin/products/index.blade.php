@extends('admin.layouts.admin')

@section('title', 'Products Management')

@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-parent-route', route('admin.dashboard'))
@section('breadcrumb-current', 'Products')

@section('admin-content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Products</h5>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Add New
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="products-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>
                                <img src="{{ asset('uploads/products/thumbnails/'. $product->product_thumbnail) }}"
                                     alt="{{ $product->product_name }}"
                                     style="width: 60px; height: 60px;"
                                     class="img-thumbnail img-rounded">
                            </td>
                            <td>{{ Str::limit($product->product_name, 20) }}</td>
                            <td>{{ $product->product_code }}</td>
                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                            <td>
                                @if($product->discount_price)
                                    <span class="text-decoration-line-through text-muted">
                                        ${{ number_format($product->selling_price, 2) }}
                                    </span>
                                    <span class="text-danger">
                                        ${{ number_format($product->discount_price, 2) }}
                                    </span>
                                @else
                                    ${{ number_format($product->selling_price, 2) }}
                                @endif
                            </td>
                            <td>{{ $product->product_qty }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input status-toggle"
                                           type="checkbox"
                                           id="status-{{ $product->id }}"
                                           data-id="{{ $product->id }}"
                                           {{ $product->status ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.products.show', $product->id) }}"
                                       class="btn btn-sm btn-info"
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                       class="btn btn-sm btn-primary"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No products found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#products-table').DataTable({
            "paging": false,
            "ordering": true,
            "info": false
        });

        // Handle status toggle
        $('.status-toggle').change(function() {
            const productId = $(this).data('id');

            $.ajax({
                url: `/admin/products/${productId}/change-status`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        });
    });
</script>
@endsection
