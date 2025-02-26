@extends('vendor.layouts.vendor')

@section('title', 'Profile')

@section('vendor')
    <div class="container py-4">
        <div class="row">
            <div class="mb-4 col-lg-4">
                <!-- Profile Card -->
                <div class="border-0 shadow-sm card">
                    <div class="text-center card-body">
                        <div class="mx-auto mb-3 position-relative" style="width: 150px;">
                            <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}"
                                class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="bottom-0 position-absolute end-0">
                                <button type="button" class="btn btn-primary btn-sm rounded-circle" data-bs-toggle="modal"
                                    data-bs-target="#photoModal">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                        </div>
                        <h4 class="fw-bold">{{ auth()->user()->name }}</h4>
                        <p class="text-muted">{{ auth()->user()->email }}</p>
                        <p class="badge bg-success">{{ ucfirst(auth()->user()->role) }}</p>

                        @if ($lastLogin)
                            <div class="mt-3 text-start">
                                <p class="mb-1 text-muted small"><i class="fas fa-clock me-1"></i> Last login:
                                    {{ $lastLogin->time_ago }}</p>
                                <p class="mb-1 text-muted small"><i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $lastLogin->location }}</p>
                                <p class="mb-0 text-muted small"><i class="fas fa-laptop me-1"></i> {{ $lastLogin->device }}
                                    - {{ $lastLogin->browser }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Sessions Card -->
                <div class="mt-4 border-0 shadow-sm card">
                    <div class="bg-white card-header">
                        <h5 class="mb-0">Active Sessions</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($sessions as $session)
                                <li class="px-0 py-3 list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="d-flex align-items-center">
                                            @if ($session->device_type == 'mobile')
                                                <i class="fas fa-mobile-alt fa-lg me-2"></i>
                                            @elseif($session->device_type == 'tablet')
                                                <i class="fas fa-tablet-alt fa-lg me-2"></i>
                                            @else
                                                <i class="fas fa-desktop fa-lg me-2"></i>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $session->device }}</div>
                                                <small class="text-muted">{{ $session->ip_address }}</small>
                                                <div class="text-muted small">{{ $session->last_activity }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($session->is_current)
                                        <span class="badge bg-primary rounded-pill">Current</span>
                                    @else
                                        <form action="{{ route('vendor.sessions.destroy', $session->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <!-- Profile Information Card -->
                <div class="mb-4 border-0 shadow-sm card">
                    <div class="bg-white card-header">
                        <h5 class="mb-0">Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('vendor.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', auth()->user()->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        id="username" name="username"
                                        value="{{ old('username', auth()->user()->username) }}">
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', auth()->user()->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', auth()->user()->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Update Password Card -->
                <div class="border-0 shadow-sm card">
                    <div class="bg-white card-header">
                        <h5 class="mb-0">Update Password</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('vendor.profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            @if (session('password_success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('password_success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    id="current_password" name="current_password">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 row">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation">
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-lock me-1"></i> Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Upload Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel">Update Profile Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <div id="image-preview" class="mx-auto"
                            style="width: 200px; height: 200px; overflow: hidden; border-radius: 50%; background-image: url('{{ auth()->user()->profile_photo_url }}'); background-size: cover; background-position: center;">
                        </div>
                    </div>

                    <form action="{{ route('vendor.profile.photo') }}" method="POST" enctype="multipart/form-data"
                        id="photoForm">
                        @csrf
                        <div class="mb-3">
                            <label for="photo" class="form-label">Choose Photo</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                            <input type="hidden" id="x" name="x">
                            <input type="hidden" id="y" name="y">
                            <input type="hidden" id="width" name="width">
                            <input type="hidden" id="height" name="height">
                        </div>
                    </form>

                    @if (auth()->user()->photo)
                        <form action="{{ route('vendor.profile.photo.delete') }}" method="POST" id="deletePhotoForm">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
                <div class="modal-footer">
                    @if (auth()->user()->photo)
                        <button type="button" class="btn btn-danger" id="deletePhotoBtn">
                            <i class="fas fa-trash me-1"></i> Delete Photo
                        </button>
                    @endif
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="savePhotoBtn">
                        <i class="fas fa-save me-1"></i> Save Photo
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-img-container {
            position: relative;
            display: inline-block;
        }

        .profile-img-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border-radius: 50%;
            padding: 5px;
            cursor: pointer;
        }

        .cropper-container {
            max-width: 100%;
        }
    </style>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cropper;
            const imagePreview = document.getElementById('image-preview');
            const photoInput = document.getElementById('photo');
            const savePhotoBtn = document.getElementById('savePhotoBtn');
            const deletePhotoBtn = document.getElementById('deletePhotoBtn');
            const photoForm = document.getElementById('photoForm');
            const deletePhotoForm = document.getElementById('deletePhotoForm');

            // Initialize cropper when a new photo is selected
            photoInput.addEventListener('change', function(e) {
                if (e.target.files.length) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        imagePreview.innerHTML = '<img src="' + event.target.result +
                            '" id="cropper-image" style="max-width: 100%;">';
                        const image = document.getElementById('cropper-image');

                        // Initialize cropper after image is loaded
                        image.onload = function() {
                            if (cropper) {
                                cropper.destroy();
                            }

                            cropper = new Cropper(image, {
                                aspectRatio: 1,
                                viewMode: 1,
                                dragMode: 'move',
                                autoCropArea: 1,
                                crop: function(event) {
                                    document.getElementById('x').value = event.detail.x;
                                    document.getElementById('y').value = event.detail.y;
                                    document.getElementById('width').value = event
                                        .detail.width;
                                    document.getElementById('height').value = event
                                        .detail.height;
                                }
                            });
                        };
                    };
                    reader.readAsDataURL(e.target.files[0]);
                }
            });

            // Save photo form submission
            savePhotoBtn.addEventListener('click', function() {
                if (photoInput.files.length) {
                    photoForm.submit();
                } else {
                    alert('Please select a photo first.');
                }
            });

            // Delete photo button
            if (deletePhotoBtn) {
                deletePhotoBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to delete your profile photo?')) {
                        deletePhotoForm.submit();
                    }
                });
            }
        });
    </script>
@endsection
