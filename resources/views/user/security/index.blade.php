{{-- resources/views/user/profile/security.blade.php --}}
@extends('user.layouts.customer')

@section('title', 'Security Settings')
@section('page-title', 'Security Settings')

@section('customer')
<div class="row">
    <div class="col-lg-8">
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
                        <label for="current_password" class="form-label">Current Password *</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" name="current_password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password *</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required minlength="8">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Password must be at least 8 characters long</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password *</label>
                        <div class="input-group">
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" required minlength="8">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Two Factor Authentication -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Two-Factor Authentication</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>SMS Authentication</h6>
                        <p class="text-muted mb-0">Get verification codes via SMS</p>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="sms2fa" disabled>
                        <label class="form-check-label" for="sms2fa">
                            <span class="badge bg-warning">Coming Soon</span>
                        </label>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Email Authentication</h6>
                        <p class="text-muted mb-0">Get verification codes via email</p>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="email2fa" disabled>
                        <label class="form-check-label" for="email2fa">
                            <span class="badge bg-warning">Coming Soon</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Security Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Security Status</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="security-score">
                        <div class="score-circle bg-success text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; border-radius: 50%;">
                            <span class="h4 mb-0">75%</span>
                        </div>
                    </div>
                    <h6 class="mt-2">Good Security</h6>
                </div>

                <div class="security-checklist">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <small>Strong password</small>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <small>Email verified</small>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-times-circle text-muted me-2"></i>
                        <small>2FA not enabled</small>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <small>Recent login activity</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Account Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download me-2"></i>Download Data
                    </button>
                    <button class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-user-lock me-2"></i>Deactivate Account
                    </button>
                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="fas fa-trash me-2"></i>Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Login History Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Login History</h5>
            </div>
            <div class="card-body">
                @if($loginHistory->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>IP Address</th>
                                    <th>Device</th>
                                    <th>Location</th>
                                    <th>os</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loginHistory as $login)
                                {{-- @dd($login) --}}
                                    <tr>
                                        <td>
                                            <div class="fw-medium">{{ $login->created_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $login->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <code>{{ $login->ip_address }}</code>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $login->device_type === 'mobile' ? 'mobile-alt' : 'desktop' }} me-2"></i>
                                                <div>
                                                    <div class="fw-medium">{{ $login->browser ?? 'Unknown' }}</div>
                                                    <small class="text-muted">{{ $login->browser_version ?? 'Unknown version' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $login->location ?? 'Unknown' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ ucfirst($login->operating_system ?: 'Unknown OS') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-2x text-muted mb-3"></i>
                        <h6>No login history available</h6>
                        <p class="text-muted">Your login history will appear here</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning!</strong> This action cannot be undone.
                </div>
                <p>Deleting your account will:</p>
                <ul>
                    <li>Permanently remove all your personal data</li>
                    <li>Cancel any active subscriptions</li>
                    <li>Remove access to your order history</li>
                    <li>Delete all saved addresses and preferences</li>
                </ul>
                <p class="mb-0">Are you sure you want to continue?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Yes, Delete My Account</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password strength indicator
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    // Add password strength logic here if needed
});
</script>
@endpush
