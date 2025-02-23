// admin/profile/index.blade.php
@extends('admin.layouts.admin')

@section('title', 'Admin Profile')
@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-parent-route', route('admin.dashboard'))

@section('admin-content')
<div class="row">
    <!-- Profile Details Card -->
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <div class="position-relative d-inline-block">
                        <img src="{{ $user->profile_photo_url }}"
                             alt="Profile"
                             class="rounded-circle avatar-xl">
                        <div class="bottom-0 avatar-xs position-absolute end-0 rounded-circle bg-primary">
                            <button class="p-0 text-white btn btn-link w-100 h-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#updateProfilePhoto">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                    </div>
                    <h5 class="mt-3 mb-1">{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->role }}</p>
                </div>

                <hr class="my-4">

                <div class="text-muted">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless text-muted">
                            <tbody>
                                <tr>
                                    <th scope="row">Email</th>
                                    <td>{{ auth()->user()->email }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Phone</th>
                                    <td>{{ auth()->user()->phone ?? 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Address</th>
                                    <td>{{ auth()->user()->address ?? 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">Last Login</th>
                                    <td>{{ session('last_login_at') ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Update Card -->
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Update Profile</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.update') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ auth()->user()->name }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ auth()->user()->username }}">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ auth()->user()->email }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ auth()->user()->phone }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="3">{{ auth()->user()->address }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Password Update Card -->
        <div class="mt-4 card">
            <div class="card-header">
                <h4 class="card-title">Change Password</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.password') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Active Sessions Card -->
        <div class="mt-4 card">
            <div class="card-header">
                <h4 class="card-title">Active Sessions</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Device</th>
                                <th>IP Address</th>
                                <th>Last Activity</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                            <tr>
                                <td>
                                    <i class="fas fa-{{ $session->device_type }} me-2"></i>
                                    {{ $session->device }}
                                </td>
                                <td>{{ $session->ip_address }}</td>
                                <td>{{ $session->last_activity }}</td>
                                <td>
                                    @if($session->is_current)
                                        <span class="badge bg-success">Current Session</span>
                                    @else
                                        <span class="badge bg-info">Active</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$session->is_current)
                                        <form action="{{ route('admin.profile.logout-session') }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="session_id" value="{{ $session->id }}">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Logout
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profile Photo Modal -->
<div class="modal fade" id="updateProfilePhoto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Profile Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <img id="image" src="" alt="Picture">
                </div>
                <input type="file" name="photo" id="profilePhotoInput" class="mt-3 form-control" accept="image/*">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="deletePhoto">Remove Photo</button>
                <button type="button" class="btn btn-primary" id="cropPhoto">Save Photo</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
<style>
     .img-container {
        max-height: 400px;
        overflow: hidden;
    }
    .img-container img {
        max-width: 100%;
        max-height: 100%;
    }
</style>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
    // Initialize Cropper.js
    let cropper;
    const image = document.getElementById('image');
    const input = document.getElementById('profilePhotoInput');
    const cropButton = document.getElementById('cropPhoto');
    const deleteButton = document.getElementById('deletePhoto');

    input.addEventListener('change', function(e) {
        const files = e.target.files;
        const reader = new FileReader();

        reader.onload = function() {
            image.src = reader.result;
            if (cropper) {
                cropper.destroy();
            }
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 2,
            });
        };

        reader.readAsDataURL(files[0]);
    });

    cropButton.addEventListener('click', function() {
        const canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300
        });

        canvas.toBlob(function(blob) {
            const formData = new FormData();
            formData.append('photo', blob, 'profile.jpg');

            fetch('{{ route("admin.profile.photo") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });
    });

    deleteButton.addEventListener('click', function() {
        if (confirm('Are you sure you want to remove your profile photo?')) {
            fetch('{{ route("admin.profile.photo.delete") }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    });
</script>
@endsection
