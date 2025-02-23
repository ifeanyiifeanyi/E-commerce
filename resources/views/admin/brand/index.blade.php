@extends('admin.layouts.admin')

@section('title', 'Brands')

@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-parent-route', route('admin.dashboard'))

@section('admin-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Brands</h3>
                        <div class="card-tools">
                            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                                Add New Brand
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>sn</th>
                                        <th>Logo</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Featured</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($brands as $brand)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <img src="{{ Storage::url($brand->logo) }}" alt="{{ $brand->name }}"
                                                    class="img-thumbnail" width="50">
                                            </td>
                                            <td>{{ $brand->name }}</td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-sm toggle-status {{ $brand->status ? 'btn-success' : 'btn-danger' }}"
                                                    data-id="{{ $brand->id }}" data-name="{{ $brand->name }}"
                                                    data-status="{{ $brand->status }}"
                                                    data-url="{{ route('admin.brands.toggle-status', $brand) }}">
                                                    {{ $brand->status ? 'Active' : 'Inactive' }}
                                                </button>
                                                <br>
                                                <small>Click to switch the status of the brand</small>
                                            </td>
                                            <td>
                                                {!! $brand->is_featured
                                                    ? '<span class="badge bg-success">Yes</span>'
                                                    : '<span class="badge bg-secondary">No</span>' !!}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary view-brand"
                                                    data-brand="{{ json_encode($brand) }}">
                                                    View
                                                </button>
                                                <a href="{{ route('admin.brands.edit', $brand) }}"
                                                    class="btn btn-sm btn-info">
                                                    Edit
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-brand"
                                                    data-id="{{ $brand->id }}" data-name="{{ $brand->name }}">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $brands->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Brand Details Modal -->
    @include('admin.brand.detail_modal')
@endsection

@section('css')

@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle View Brand Modal
            const viewButtons = document.querySelectorAll('.view-brand');
            const brandDetailsModal = new bootstrap.Modal(document.getElementById('brandDetailsModal'));

            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const brand = JSON.parse(this.dataset.brand);

                    // Update modal content
                    document.getElementById('brandLogo').src = `/storage/${brand.logo}`;
                    document.getElementById('brandName').textContent = brand.name;
                    document.getElementById('brandWebsite').textContent = brand.website || 'N/A';
                    document.getElementById('brandDescription').textContent = brand.description ||
                        'N/A';
                    document.getElementById('brandStatus').textContent = brand.status ? 'Active' :
                        'Inactive';
                    document.getElementById('brandFeatured').textContent = brand.is_featured ?
                        'Yes' : 'No';
                    document.getElementById('brandMetaTitle').textContent = brand.meta_title ||
                        'N/A';
                    document.getElementById('brandMetaDescription').textContent = brand
                        .meta_description || 'N/A';
                    document.getElementById('brandMetaKeywords').textContent = brand
                        .meta_keywords || 'N/A';

                    brandDetailsModal.show();
                });
            });

            // Handle Delete Brand
            const deleteButtons = document.querySelectorAll('.delete-brand');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const brandId = this.dataset.id;
                    const brandName = this.dataset.name;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: `Do you want to delete ${brandName}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Create and submit form programmatically
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/admin/destroy/${brandId}/brand`;
                            form.innerHTML = `
                                @csrf
                                @method('DELETE')
                            `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });


            // Handle Status Toggle
            const toggleButtons = document.querySelectorAll('.toggle-status');

            toggleButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const brandName = this.dataset.name;
                    const currentStatus = this.dataset.status === '1';
                    const newStatus = !currentStatus;
                    const url = this.dataset.url;

                    Swal.fire({
                        title: 'Confirm Status Change',
                        text: `Do you want to ${newStatus ? 'activate' : 'deactivate'} ${brandName}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: newStatus ? '#28a745' : '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: `Yes, ${newStatus ? 'activate' : 'deactivate'} it!`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Create and submit form programmatically
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = url;
                            form.innerHTML = `
                                @csrf
                                @method('PATCH')
                            `;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
