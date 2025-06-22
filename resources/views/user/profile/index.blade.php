@extends('user.layouts.customer')

@section('page-title', Auth::user()->name . ' - Profile')
@section('title', Auth::user()->name . ' - Profile')

@section('customer')
<div class="row">
    <div class="col-lg-4 mb-4">
        <!-- Profile Card -->
        <div class="card">
            <div class="card-body text-center">
                <div class="position-relative d-inline-block mb-3">
                    <img src="{{ $user->profile_photo_url }}" alt="Profile Photo" class="profile-avatar">
                    <button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle" data-bs-toggle="modal" data-bs-target="#photoModal">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                <h4>{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>

                <div class="row text-center mt-4">
                    <div class="col-4">
                        {{-- <strong>{{ $user->orders()->count() ?? 0 }}</strong> --}}
                        <br><small class="text-muted">Orders</small>
                    </div>
                    <div class="col-4">
                        <strong>{{ $user->addresses()->count() }}</strong>
                        <br><small class="text-muted">Addresses</small>
                    </div>
                    <div class="col-4">
                        <strong>{{ ceil($user->created_at->diffInDays()) }}</strong>
                        <br><small class="text-muted">Days</small>
                    </div>
                </div>

                <div class="mt-4">
                    <span class="badge bg-{{ $user->account_status === 'active' ? 'success' : 'warning' }} mb-2">
                        {{ ucfirst($user->account_status ?? 'active') }}
                    </span>
                    <br>
                    <span class="badge bg-info">
                        {{ ucfirst($user->customer_segment ?? 'regular') }} Customer
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Account Stats</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 text-center">
                        <i class="fas fa-calendar-alt text-primary fa-2x mb-2"></i>
                        <h6>Member Since</h6>
                        <small class="text-muted">{{ $user->created_at->format('M Y') }}</small>
                    </div>
                    <div class="col-6 text-center">
                        <i class="fas fa-clock text-success fa-2x mb-2"></i>
                        <h6>Last Login</h6>
                        <small class="text-muted">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Profile Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Profile Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                   id="username" name="username" value="{{ old('username', $user->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror"
                               id="address" name="address" value="{{ old('address', $user->address) }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror"
                                   id="city" name="city" value="{{ old('city', $user->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror"
                                   id="state" name="state" value="{{ old('state', $user->state) }}">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="postal_code" class="form-label">Postal Code</label>
                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                                   id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country</label>
                            <select name="country" id="country" class="form-control">
                                @foreach($countries as $country)
                                    <option value="{{ $country->name }}" {{ old('country', $user->country) == $country->name ? 'selected' : '' }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Marketing Preferences -->
                    <div class="mb-3">
                        <label class="form-label">Marketing Preferences</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="marketing_preferences[]"
                                           value="email" id="pref_email"
                                           {{ in_array('email', old('marketing_preferences', $user->marketing_preferences ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pref_email">Email</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="marketing_preferences[]"
                                           value="sms" id="pref_sms"
                                           {{ in_array('sms', old('marketing_preferences', $user->marketing_preferences ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pref_sms">SMS</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="marketing_preferences[]"
                                           value="push" id="pref_push"
                                           {{ in_array('push', old('marketing_preferences', $user->marketing_preferences ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pref_push">Push</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="marketing_preferences[]"
                                           value="newsletter" id="pref_newsletter"
                                           {{ in_array('newsletter', old('marketing_preferences', $user->marketing_preferences ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pref_newsletter">Newsletter</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('user.profile.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                               id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('user.addresses') }}" class="btn btn-primary w-100">
                            <i class="fas fa-map-marker-alt mb-2 d-block"></i>
                            Addresses
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('user.security') }}" class="btn btn-info w-100">
                            <i class="fas fa-shield-alt mb-2 d-block"></i>
                            Security
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('user.activity') }}" class="btn btn-success w-100">
                            <i class="fas fa-history mb-2 d-block"></i>
                            Activity Log
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('user.notifications') }}" class="btn btn-warning w-100">
                            <i class="fas fa-bell mb-2 d-block"></i>
                            Notifications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.layouts.partials.photo-model')

@endsection

@push('styles')
<style>
.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #f8f9fa;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card {
    border: none;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    border-radius: 10px;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px 10px 0 0 !important;
}

.btn-outline-primary:hover,
.btn-outline-info:hover,
.btn-outline-success:hover,
.btn-outline-warning:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.badge {
    font-size: 0.8em;
    padding: 0.5em 0.8em;
}

.fa-2x {
    color: #667eea;
}

.quick-actions .btn {
    min-height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
</style>
@endpush

@push('scripts')
<script>
// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');

    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Passwords don't match");
        } else {
            confirmPassword.setCustomValidity('');
        }
    }

    if (password && confirmPassword) {
        password.addEventListener('change', validatePassword);
        confirmPassword.addEventListener('keyup', validatePassword);
    }

    // Show success message
    @if(session('success'))
        // You can customize this to use your preferred notification system
        alert('{{ session('success') }}');
    @endif
});

// Photo preview function (if not already included in modal)
function previewPhoto(input) {
    const previewContainer = document.querySelector('.preview-container');
    const preview = document.getElementById('photoPreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }

        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
    }
}
</script>
@endpush
