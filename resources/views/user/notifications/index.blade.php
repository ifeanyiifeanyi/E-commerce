{{-- resources/views/user/notifications/index.blade.php --}}
@extends('user.layouts.customer')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('customer')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notifications</h5>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="notificationFilter" style="width: auto;">
                        <option value="">All Notifications</option>
                        <option value="order">Order Updates</option>
                        <option value="account">Account</option>
                        <option value="security">Security</option>
                        <option value="marketing">Marketing</option>
                        <option value="system">System</option>
                    </select>
                    <form method="POST" action="{{ route('user.notifications.read-all') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-check-double me-1"></i>Mark All Read
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                @if($notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                            <div class="list-group-item {{ $notification->isUnread() ? 'list-group-item-light border-start border-primary border-3' : '' }} notification-item"
                                 data-type="{{ $notification->notification_type }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex">
                                        <div class="notification-icon me-3">
                                            <div class="icon-circle bg-{{ $notification->isUnread() ? 'primary' : 'secondary' }}">
                                                <i class="fas fa-{{ getNotificationIcon($notification->notification_type) }}"></i>
                                            </div>
                                        </div>
                                        <div class="notification-content">
                                            <h6 class="notification-title mb-1">
                                                {{ $notification->title }}
                                                @if($notification->isUnread())
                                                    <span class="badge bg-primary ms-2">New</span>
                                                @endif
                                            </h6>
                                            <p class="notification-message mb-2 text-muted">
                                                {{ $notification->message }}
                                            </p>
                                            <div class="notification-meta">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </small>
                                                <span class="badge bg-{{ getNotificationTypeColor($notification->notification_type) }} ms-2">
                                                    {{ ucfirst($notification->notification_type) }}
                                                </span>
                                                @if($notification->read_at)
                                                    <small class="text-muted ms-2">
                                                        Read {{ $notification->read_at->diffForHumans() }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="notification-actions">
                                        @if($notification->isUnread())
                                            <button class="btn btn-sm btn-outline-primary me-2"
                                                    onclick="markAsRead({{ $notification->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        @if($notification->link_url)
                                            <a href="{{ $notification->link_url }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                        <div class="dropdown d-inline">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if($notification->isUnread())
                                                    <li>
                                                        <button class="dropdown-item" onclick="markAsRead({{ $notification->id }})">
                                                            <i class="fas fa-check me-2"></i>Mark as Read
                                                        </button>
                                                    </li>
                                                @endif
                                                <li>
                                                    <button class="dropdown-item text-danger" onclick="deleteNotification({{ $notification->id }})">
                                                        <i class="fas fa-trash me-2"></i>Delete
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="card-footer">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h5>No notifications yet</h5>
                        <p class="text-muted">You'll see important updates and messages here</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Notification Statistics -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-bell fa-2x text-primary mb-2"></i>
                <h4>{{ $notifications->total() }}</h4>
                <small class="text-muted">Total Notifications</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-envelope fa-2x text-info mb-2"></i>
                <h4>{{ $notifications->where('read_at', null)->count() }}</h4>
                <small class="text-muted">Unread</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-shopping-cart fa-2x text-success mb-2"></i>
                <h4>{{ $notifications->where('notification_type', 'order')->count() }}</h4>
                <small class="text-muted">Order Updates</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-shield-alt fa-2x text-warning mb-2"></i>
                <h4>{{ $notifications->where('notification_type', 'security')->count() }}</h4>
                <small class="text-muted">Security Alerts</small>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.notification-item {
    transition: all 0.3s ease;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-icon .icon-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.notification-title {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.notification-message {
    font-size: 13px;
    line-height: 1.4;
}

.notification-meta {
    font-size: 12px;
}

.notification-actions {
    white-space: nowrap;
}

@media (max-width: 768px) {
    .notification-actions {
        margin-top: 10px;
    }

    .d-flex.justify-content-between {
        flex-direction: column;
    }
}
</style>
@endpush

@push('scripts')
<script>
function markAsRead(notificationId) {
    fetch(`/customer/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to mark notification as read');
    });
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        // Implement delete functionality
        alert('Delete functionality will be implemented soon!');
    }
}

// Filter notifications
document.getElementById('notificationFilter').addEventListener('change', function() {
    const filter = this.value.toLowerCase();
    const notifications = document.querySelectorAll('.notification-item');

    notifications.forEach(notification => {
        const type = notification.getAttribute('data-type').toLowerCase();

        if (filter === '' || type === filter) {
            notification.style.display = 'block';
        } else {
            notification.style.display = 'none';
        }
    });
});
</script>
@endpush

@php
function getNotificationIcon($type) {
    $icons = [
        'order' => 'shopping-cart',
        'account' => 'user',
        'security' => 'shield-alt',
        'marketing' => 'bullhorn',
        'system' => 'cog',
        'default' => 'bell'
    ];

    return $icons[$type] ?? $icons['default'];
}

function getNotificationTypeColor($type) {
    $colors = [
        'order' => 'success',
        'account' => 'info',
        'security' => 'danger',
        'marketing' => 'warning',
        'system' => 'secondary'
    ];

    return $colors[$type] ?? 'primary';
}
@endphp
