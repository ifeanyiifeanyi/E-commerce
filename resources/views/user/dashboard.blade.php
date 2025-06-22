@extends('user.layouts.customer')


@section('page-title', 'Dashboard')
@section('title', 'Customer Dashboard')
@section('customer')
    <div class="row">
    <!-- Stats Cards -->
    <div class="col-md-3 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-shopping-bag fa-2x mb-3"></i>
                <h3 class="mb-2">0</h3>
                <p class="mb-0">Total Orders</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card stats-card success">
            <div class="card-body text-center">
                <i class="fas fa-heart fa-2x mb-3"></i>
                <h3 class="mb-2">0</h3>
                <p class="mb-0">Wishlist Items</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card stats-card warning">
            <div class="card-body text-center">
                <i class="fas fa-map-marker-alt fa-2x mb-3"></i>
                <h3 class="mb-2">{{ $user->addresses()->count() }}</h3>
                <p class="mb-0">Saved Addresses</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card stats-card info">
            <div class="card-body text-center">
                <i class="fas fa-bell fa-2x mb-3"></i>
                <h3 class="mb-2">{{ $unreadNotifications }}</h3>
                <p class="mb-0">Notifications</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Profile Completion -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>Profile Completion</h5>
                <small class="text-muted">{{ $user->hasCompleteProfile() ? '100%' : '70%' }} Complete</small>
            </div>
            <div class="card-body">
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar" style="width: {{ $user->hasCompleteProfile() ? '100%' : '70%' }}"></div>
                </div>

                @if(!$user->hasCompleteProfile())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Complete your profile to get the most out of our platform!</strong>
                        <br>
                        <small>Missing information:
                            @if(!$user->phone) Phone, @endif
                            @if(!$user->address) Address @endif
                        </small>
                    </div>
                    <a href="{{ route('user.profile') }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Complete Profile
                    </a>
                @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Your profile is complete! Great job.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('user.profile') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-edit me-2"></i>Edit Profile
                    </a>
                    <a href="{{ route('user.addresses') }}" class="btn btn-outline-success">
                        <i class="fas fa-map-marker-alt me-2"></i>Manage Addresses
                    </a>
                    <a href="{{ route('user.security') }}" class="btn btn-outline-warning">
                        <i class="fas fa-shield-alt me-2"></i>Security Settings
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="fas fa-headset me-2"></i>Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Activity -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h5>
                <a href="{{ route('user.activity') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @forelse($recentActivities as $activity)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $activity->description }}</strong>
                                @if($activity->properties)
                                    {{-- <br><code class="text-muted">{{ json_encode($activity->properties) }}</code> --}}
                                @endif
                            </div>
                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-clock fa-2x mb-3"></i>
                        <p>No recent activity found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Notifications -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Recent Notifications</h5>
                <a href="{{ route('user.notifications') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @forelse($notifications as $notification)
                    <div class="d-flex align-items-start mb-3 p-3 {{ $notification->isUnread() ? 'bg-light' : '' }} rounded">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $notification->title }}</h6>
                            <p class="mb-1 text-muted">{{ Str::limit($notification->message, 100) }}</p>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        @if($notification->isUnread())
                            <span class="badge bg-primary">New</span>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-bell-slash fa-2x mb-3"></i>
                        <p>No notifications found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Recent Login History -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Recent Login History</h5>
            </div>
            <div class="card-body">
                @if($recentLogins->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>IP Address</th>
                                    <th>Device</th>
                                    <th>Browser</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLogins as $login)
                                    <tr>
                                        <td>{{ $login->created_at->format('M d, Y H:i') }}</td>
                                        <td><code>{{ $login->ip_address }}</code></td>
                                        <td>{{ $login->device_info ?: 'Unknown' }}</td>
                                        <td>{{ $login->browser_info ?: 'Unknown' }}</td>
                                        <td>{{ $login->location ?: 'Unknown' }}</td>
                                        <td>
                                            @if($login->successful)
                                                <span class="badge bg-success">Success</span>
                                            @else
                                                <span class="badge bg-danger">Failed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-history fa-2x mb-3"></i>
                        <p>No login history available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')

@endpush

@push('scripts')

@endpush
