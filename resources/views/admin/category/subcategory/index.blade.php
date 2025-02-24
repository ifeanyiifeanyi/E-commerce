@extends('admin.layouts.admin')

@section('title', 'Subcategories')

@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-parent-route', route('admin.dashboard'))

@section('admin-content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Subcategories</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSubcategoryModal">
                <i class="bi bi-plus"></i> Add Subcategory
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subcategories as $subcategory)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $subcategory->category->name }}</td>
                                <td>{{ $subcategory->name }}</td>
                                <td>{{ Str::limit($subcategory->description, 50) }}</td>
                                <td>
                                    @if ($subcategory->image)
                                        <img src="{{ Storage::url($subcategory->image) }}" alt="{{ $subcategory->name }}"
                                            class="img-thumbnail" width="50">
                                    @else
                                        <span class="text-muted">No image</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info view-subcategory"
                                        data-bs-toggle="modal" data-bs-target="#viewSubcategoryModal"
                                        data-subcategory="{{ json_encode($subcategory) }}"
                                        data-image="{{ $subcategory->image ? Storage::url($subcategory->image) : '' }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary edit-subcategory"
                                        data-bs-toggle="modal" data-bs-target="#editSubcategoryModal"
                                        data-subcategory="{{ json_encode($subcategory) }}"
                                        data-image="{{ $subcategory->image ? Storage::url($subcategory->image) : '' }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-subcategory"
                                        data-subcategory-id="{{ $subcategory->id }}"
                                        data-subcategory-name="{{ $subcategory->name }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No subcategories found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {{ $subcategories->links() }}
            </div>
        </div>
    </div>

    <!-- Create Subcategory Modal -->
    @include('admin.category.subcategory.create')
    <!-- View Subcategory Modal -->
    @include('admin.category.subcategory.show')

    <!-- Edit Subcategory Modal -->
    @include('admin.category.subcategory.edit')
@endsection



@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
@endsection


@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show validation errors in modal if they exist
            @if ($errors->any())
                const modalId =
                    '{{ old('_method') === 'PUT' ? 'editSubcategoryModal' : 'createSubcategoryModal' }}';
                const modal = new bootstrap.Modal(document.getElementById(modalId));
                modal.show();
            @endif

            // View Subcategory
            document.querySelectorAll('.view-subcategory').forEach(button => {
                button.addEventListener('click', function() {
                    const subcategory = JSON.parse(this.dataset.subcategory);
                    const image = this.dataset.image;

                    document.getElementById('view-subcategory-category').textContent = subcategory
                        .category.name;
                    document.getElementById('view-subcategory-name').textContent = subcategory.name;
                    document.getElementById('view-subcategory-description').textContent =
                        subcategory.description || 'No description';
                    document.getElementById('view-subcategory-status').textContent = subcategory
                        .is_active ? 'Active' : 'Inactive';

                    const imageElement = document.getElementById('view-subcategory-image');
                    if (image) {
                        imageElement.src = image;
                        imageElement.style.display = 'block';
                    } else {
                        imageElement.style.display = 'none';
                    }
                });
            });

            // Edit Subcategory
            document.querySelectorAll('.edit-subcategory').forEach(button => {
                button.addEventListener('click', function() {
                    const subcategory = JSON.parse(this.dataset.subcategory);
                    const image = this.dataset.image;

                    const form = document.getElementById('editSubcategoryForm');
                    form.action = `/admin/subcategory/${subcategory.id}`;

                    document.getElementById('edit-category_id').value = subcategory.category_id;
                    document.getElementById('edit-name').value = subcategory.name;
                    document.getElementById('edit-description').value = subcategory.description;
                    document.getElementById('edit-is_active').checked = subcategory.is_active;

                    const currentImage = document.querySelector('#current-image img');
                    if (image) {
                        currentImage.src = image;
                        currentImage.style.display = 'block';
                    } else {
                        currentImage.style.display = 'none';
                    }
                });
            });

            // Client-side validation
            const validateForm = (form) => {
                let isValid = true;

                // Category validation
                const categorySelect = form.querySelector('[name="category_id"]');
                if (!categorySelect.value) {
                    categorySelect.classList.add('is-invalid');
                    const feedback = categorySelect.nextElementSibling || document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Please select a category.';
                    if (!categorySelect.nextElementSibling) {
                        categorySelect.parentNode.appendChild(feedback);
                    }
                    isValid = false;
                } else {
                    categorySelect.classList.remove('is-invalid');
                }

                // Name validation
                const nameInput = form.querySelector('[name="name"]');
                if (!nameInput.value.trim()) {
                    nameInput.classList.add('is-invalid');
                    const feedback = nameInput.nextElementSibling || document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'The name field is required.';
                    if (!nameInput.nextElementSibling) {
                        nameInput.parentNode.appendChild(feedback);
                    }
                    isValid = false;
                } else {
                    nameInput.classList.remove('is-invalid');
                }

                // Image validation
                const imageInput = form.querySelector('[name="image"]');
                if (imageInput.files.length > 0) {
                    const file = imageInput.files[0];
                    const maxSize = 2 * 1024 * 1024; // 2MB
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];

                    if (file.size > maxSize) {
                        imageInput.classList.add('is-invalid');
                        const feedback = imageInput.nextElementSibling || document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'The image must not be greater than 2MB.';
                        if (!imageInput.nextElementSibling) {
                            imageInput.parentNode.appendChild(feedback);
                        }
                        isValid = false;
                    } else if (!allowedTypes.includes(file.type)) {
                        imageInput.classList.add('is-invalid');
                        const feedback = imageInput.nextElementSibling || document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'The image must be a file of type: jpeg, png, jpg, gif.';
                        if (!imageInput.nextElementSibling) {
                            imageInput.parentNode.appendChild(feedback);
                        }
                        isValid = false;
                    } else {
                        imageInput.classList.remove('is-invalid');
                    }
                }

                return isValid;
            };

            // Add form validation to create form
            document.getElementById('createSubcategoryForm').addEventListener('submit', function(e) {
                if (!validateForm(this)) {
                    e.preventDefault();
                }
            });

            // Add form validation to edit form
            document.getElementById('editSubcategoryForm').addEventListener('submit', function(e) {
                if (!validateForm(this)) {
                    e.preventDefault();
                }
            });

            // Delete Subcategory
            document.querySelectorAll('.delete-subcategory').forEach(button => {
                button.addEventListener('click', function() {
                    const subcategoryId = this.dataset.subcategoryId;
                    const subcategoryName = this.dataset.subcategoryName;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: `You want to delete subcategory "${subcategoryName}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const csrfToken = document.querySelector(
                                'meta[name="csrf-token"]')?.content;

                            if (!csrfToken) {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'CSRF token not found. Please refresh the page.',
                                    icon: 'error'
                                });
                                return;
                            }

                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = `/admin/subcategory/${subcategoryId}/destroy`;

                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;
                            form.appendChild(csrfInput);

                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'DELETE';
                            form.appendChild(methodInput);

                            document.body.appendChild(form);
                            form.submit();
                        }
                    }).catch(error => {
                        console.error('Delete operation failed:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error'
                        });
                    });
                });
            });
        });
    </script>
@endsection
