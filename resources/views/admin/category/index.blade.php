@extends('admin.layouts.admin')

@section('title', 'Categories')

@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-parent-route', route('admin.dashboard'))

@section('admin-content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Categories</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus"></i> Add Category
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>sn</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @dd($categories) --}}
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $category->name }}
                                    &nbsp;
                                    <small class="badge bg-primary">{{ $category->subcategories_count }}</small>
                                </td>
                                <td>{{ Str::limit($category->description, 50) }}</td>

                                <td>
                                    @if ($category->image)
                                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}"
                                            class="img-thumbnail" width="50">
                                    @else
                                        <span class="text-muted">No image</span>
                                    @endif
                                </td>
                                <td class="text-center btn-group">
                                    <button type="button" class="btn btn-sm btn-info view-category" data-bs-toggle="modal"
                                        data-bs-target="#viewCategoryModal" data-category="{{ json_encode($category) }}"
                                        data-image="{{ $category->image ? Storage::url($category->image) : '' }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary edit-category"
                                        data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                        data-category="{{ json_encode($category) }}"
                                        data-image="{{ $category->image ? Storage::url($category->image) : '' }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-category"
                                        data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No categories found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <!-- Create Category Modal -->
    @include('admin.category.create')

    <!-- View Category Modal -->
    @include('admin.category.show')

    <!-- Edit Category Modal -->
    @include('admin.category.edit')
@endsection



@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View Category
        document.querySelectorAll('.view-category').forEach(button => {
            button.addEventListener('click', function() {
                const category = JSON.parse(this.dataset.category);
                const image = this.dataset.image;

                document.getElementById('view-category-name').textContent = category.name;
                document.getElementById('view-category-description').textContent = category.description || 'No description';
                document.getElementById('view-category-status').textContent = category.is_active ? 'Active' : 'Inactive';

                const imageElement = document.getElementById('view-category-image');
                if (image) {
                    imageElement.src = image;
                    imageElement.style.display = 'block';
                } else {
                    imageElement.style.display = 'none';
                }
            });
        });

        // Edit Category
        document.querySelectorAll('.edit-category').forEach(button => {
            button.addEventListener('click', function() {
                const category = JSON.parse(this.dataset.category);
                const image = this.dataset.image;

                const form = document.getElementById('editCategoryForm');
                form.action = `/admin/category/${category.id}`;

                document.getElementById('edit-name').value = category.name;
                document.getElementById('edit-description').value = category.description;
                document.getElementById('edit-is_active').checked = category.is_active;

                const currentImage = document.querySelector('#current-image img');
                if (image) {
                    currentImage.src = image;
                    currentImage.style.display = 'block';
                } else {
                    currentImage.style.display = 'none';
                }
            });
        });

        // Delete Category
        document.querySelectorAll('.delete-category').forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.dataset.categoryId;
                const categoryName = this.dataset.categoryName;

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You want to delete category "${categoryName}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/category/${categoryId}/destroy`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;

                        const methodField = document.createElement('input');
                        methodField.type = 'hidden';
                        methodField.name = '_method';
                        methodField.value = 'DELETE';

                        form.appendChild(csrfToken);
                        form.appendChild(methodField);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    });
    </script>
@endsection
