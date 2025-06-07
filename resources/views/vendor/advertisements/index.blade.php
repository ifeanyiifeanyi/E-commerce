@extends('vendor.layouts.vendor')

@section('title', 'Advertisement Dashboard')

@section('vendor')
    <div class="container-fluid px-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Advertisement Dashboard</h1>
                <p class="text-muted">Manage your advertising campaigns and track performance</p>
            </div>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAdModal">
                    <i class="fas fa-plus me-2"></i>Create New Ad
                </button>
            </div>
        </div>

        <!-- Notifications Alert -->
        @if ($notifications && $notifications->count() > 0)
            <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-bell me-2"></i>
                You have {{ $notifications->count() }} unread notification(s) about your advertisements.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Spent
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₦{{ number_format($stats['total_spent'] ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Active Ads
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['active_ads'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-ad fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Impressions
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_impressions'] ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-eye fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Click Rate (CTR)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['average_ctr'] ?? 0, 2) }}%
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-mouse-pointer fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Packages Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Available Advertisement Packages</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="{{ route('vendor.advertisements.packages') }}">View All Packages</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($packages as $package)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 {{ $package->isAvailable() ? 'border-success' : 'border-secondary' }}">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 font-weight-bold">{{ $package->name }}</h6>
                                        @if ($package->isAvailable())
                                            <span class="badge bg-success">Available</span>
                                        @else
                                            <span class="badge bg-secondary">Full</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <small class="text-muted">Location:</small>
                                        <strong>{{ $package->location_display }}</strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Duration:</small>
                                        <strong>{{ $package->duration_days }} days</strong>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Available Slots:</small>
                                        <strong>{{ $package->available_slots }}/{{ $package->max_slots }}</strong>
                                    </div>
                                    <div class="progress mb-3" style="height: 5px;">
                                        <div class="progress-bar bg-info" role="progressbar"
                                            style="width: {{ ($package->activeAdvertisements->count() / $package->max_slots) * 100 }}%">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0 text-primary">₦{{ number_format($package->price, 2) }}</h5>
                                        <button class="btn btn-sm" style="border-color: peru; color: peru;"
                                            data-bs-toggle="modal" data-bs-target="#packageDetailModal{{ $package->id }}">
                                            View Details
                                        </button>
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <div class="d-grid">
                                        @if ($package->isAvailable())
                                            <button class="btn btn-sm" style="background: peru; color: white;"
                                                onclick="subscribeToPackage({{ $package->id }})">
                                                Subscribe Now
                                            </button>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                Not Available
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-4">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No packages available</h5>
                                <p class="text-muted">Check back later for new advertisement packages.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Package Detail Modals -->
        @foreach ($packages as $package)
            <div class="modal fade" id="packageDetailModal{{ $package->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $package->name }} - Package Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Package Name:</strong></td>
                                            <td>{{ $package->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Location:</strong></td>
                                            <td>{{ $package->location_display }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Price:</strong></td>
                                            <td class="text-primary">
                                                <strong>₦{{ number_format($package->price, 2) }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Duration:</strong></td>
                                            <td>{{ $package->duration_days }} days</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Slots:</strong></td>
                                            <td>{{ $package->max_slots }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Available Slots:</strong></td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $package->available_slots > 0 ? 'success' : 'danger' }}">
                                                    {{ $package->available_slots }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Package Features:</h6>
                                    @if ($package->features && count($package->features) > 0)
                                        <ul class="list-unstyled">
                                            @foreach ($package->features as $feature)
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    {{ $feature }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted">No specific features listed.</p>
                                    @endif

                                    <div class="mt-3">
                                        <h6>Availability Progress:</h6>
                                        <div class="progress mb-2" style="height: 10px;">
                                            <div class="progress-bar bg-info" role="progressbar"
                                                style="width: {{ ($package->activeAdvertisements->count() / $package->max_slots) * 100 }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $package->activeAdvertisements->count() }} of {{ $package->max_slots }}
                                            slots occupied
                                        </small>
                                    </div>
                                </div>
                            </div>

                            @if ($package->description)
                                <hr>
                                <div>
                                    <h6>Description:</h6>
                                    <p class="text-muted">{{ $package->description }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            @if ($package->isAvailable())
                                <button type="button" class="btn btn-primary"
                                    onclick="subscribeToPackage({{ $package->id }})">
                                    Subscribe to This Package
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary" disabled>
                                    Package Not Available
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- My Advertisements Section -->
        <!-- My Advertisements Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">My Advertisements</h6>
                <div class="d-flex align-items-center">
                    <div class="dropdown me-2">
                        <select class="form-select form-select-sm" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="rejected">Rejected</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" onclick="refreshTable()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if ($advertisements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Title</th>
                                    <th>Package</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($advertisements as $index => $ad)
                                    <tr data-status="{{ $ad->status }}">
                                        <td>
                                            <div class="text-center">
                                                <strong>#{{ $ad->id }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <h6 class="mb-1">{{ Str::title($ad->title) }}</h6>
                                            <small class="text-muted">
                                                Amount Paid:
                                                <strong
                                                    class="badge bg-success">₦{{ number_format($ad->amount_paid, 2) }}</strong>
                                            </small>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $ad->package->name ?? 'N/A' }}</strong><br>
                                                <small
                                                    class="badge bg-primary">{{ $ad->package->location_display ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <strong>{{ $ad->start_date->format('M d') }}</strong> -
                                                <strong>{{ $ad->end_date->format('M d, Y') }}</strong>
                                            </div>
                                            @if ($ad->isActive())
                                                <div class="small text-success">
                                                    <i class="fas fa-clock"></i>
                                                    {{ $ad->days_remaining }} days left
                                                </div>
                                            @elseif($ad->isExpired())
                                                <div class="small text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Expired
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match ($ad->status) {
                                                    'active' => 'success',
                                                    'pending' => 'warning',
                                                    'rejected' => 'danger',
                                                    'expired' => 'secondary',
                                                    'paused' => 'info',
                                                    default => 'light',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucfirst($ad->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="">
                                                <button type="button" class="btn"
                                                    style="background: peru; color: white;" data-bs-toggle="modal"
                                                    data-bs-target="#adDetailModal{{ $ad->id }}"
                                                    title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if ($ad->status !== 'expired')
                                                    <a href="{{ route('vendor.advertisements.edit', $ad->id) }}"
                                                        class="btn btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if ($ad->payment_status === \App\Models\VendorAdvertisement::PAYMENT_STATUS_COMPLETED)
                                                    <a href="{{ route('vendor.advertisements.cancel', $ad->id) }}"
                                                        style="background:purple;color:white" class="btn"
                                                        onclick="return cancelAdvertisement({{ $ad->id }}, event)">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                @endif
                                                @if ($ad->canBeDeleted())
                                                    <button type="button" class="btn btn-danger"
                                                        onclick="deleteAdvertisement({{ $ad->id }})"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-ad fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">No Advertisements Yet</h4>
                        <p class="text-muted mb-4">Start promoting your products by creating your first advertisement.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAdModal">
                            <i class="fas fa-plus me-2"></i>Create Your First Ad
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @foreach ($advertisements as $ad)
        <div class="modal fade" id="adDetailModal{{ $ad->id }}" tabindex="-1"
            aria-labelledby="adDetailModalLabel{{ $ad->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="adDetailModalLabel{{ $ad->id }}">
                            Advertisement Details - {{ $ad->title }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Advertisement Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Title:</strong></td>
                                                <td>{{ $ad->title }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Package:</strong></td>
                                                <td>{{ $ad->package->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Location:</strong></td>
                                                <td>{{ $ad->package->location_display ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Amount Paid:</strong></td>
                                                <td class="text-success">
                                                    <strong>₦{{ number_format($ad->amount_paid, 2) }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    @php
                                                        $statusClass = match ($ad->status) {
                                                            'active' => 'success',
                                                            'pending' => 'warning',
                                                            'rejected' => 'danger',
                                                            'expired' => 'secondary',
                                                            'paused' => 'info',
                                                            default => 'light',
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $statusClass }}">
                                                        {{ ucfirst($ad->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>

                                        @if ($ad->image_path)
                                            <div class="mb-3 text-center">
                                                <img src="{{ asset($ad->image_url) }}" alt="Advertisement"
                                                    class="img-fluid rounded" style="max-height: 200px;">
                                            </div>
                                        @endif

                                        @if ($ad->description)
                                            <div class="mt-3">
                                                <h6>Description:</h6>
                                                <p class="text-muted">{{ $ad->description }}</p>
                                            </div>
                                        @endif

                                        @if ($ad->rejection_reason)
                                            <div class="mt-3">
                                                <h6>Rejection Reason:</h6>
                                                <div class="alert alert-danger mb-0">
                                                    {{ $ad->rejection_reason }}
                                                </div>
                                            </div>
                                        @endif

                                        @if ($ad->admin_notes)
                                            <div class="mt-3">
                                                <h6>Admin Notes:</h6>
                                                <div class="alert alert-info mb-0">
                                                    {{ $ad->admin_notes }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Campaign Timeline & Performance</h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Timeline -->
                                        <div class="mb-4">
                                            <h6>Campaign Duration:</h6>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span><strong>Start:</strong>
                                                    {{ $ad->start_date->format('M d, Y H:i') }}</span>
                                                <span><strong>End:</strong>
                                                    {{ $ad->end_date->format('M d, Y H:i') }}</span>
                                            </div>

                                            @if ($ad->isActive())
                                                <div class="progress mb-2" style="height: 8px;">
                                                    @php
                                                        $totalDays = $ad->start_date->diffInDays($ad->end_date);
                                                        $daysPassed = $ad->start_date->diffInDays(now());
                                                        $progressPercent =
                                                            $totalDays > 0
                                                                ? min(100, ($daysPassed / $totalDays) * 100)
                                                                : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ $progressPercent }}%"></div>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">{{ $daysPassed }} days passed</small>
                                                    <small class="text-success">{{ $ad->days_remaining }} days
                                                        remaining</small>
                                                </div>
                                            @elseif($ad->isExpired())
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    This advertisement campaign has expired.
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Performance Stats -->
                                        <div class="row text-center mb-4">
                                            <div class="col-4">
                                                <div class="border rounded p-3">
                                                    <div class="text-info">
                                                        <i class="fas fa-eye fa-2x mb-2"></i>
                                                    </div>
                                                    <h4 class="mb-1">{{ number_format($ad->impressions ?? 0) }}</h4>
                                                    <small class="text-muted">Total Views</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="border rounded p-3">
                                                    <div class="text-success">
                                                        <i class="fas fa-mouse-pointer fa-2x mb-2"></i>
                                                    </div>
                                                    <h4 class="mb-1">{{ number_format($ad->clicks ?? 0) }}</h4>
                                                    <small class="text-muted">Total Clicks</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="border rounded p-3">
                                                    <div class="text-warning">
                                                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                                                    </div>
                                                    <h4 class="mb-1">{{ number_format($ad->ctr ?? 0, 2) }}%</h4>
                                                    <small class="text-muted">Click Rate</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Information -->
                                        @if (isset($ad->payments) && $ad->payments->count() > 0)
                                            <div>
                                                <h6>Payment History:</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Amount</th>
                                                                <th>Method</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($ad->payments as $payment)
                                                                <tr>
                                                                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                                                    <td>₦{{ number_format($payment->amount, 2) }}</td>
                                                                    <td>{{ ucfirst($payment->payment_method) }}</td>
                                                                    <td>
                                                                        <span
                                                                            class="badge bg-{{ $payment->payment_status === 'completed' ? 'success' : 'warning' }}">
                                                                            {{ ucfirst($payment->payment_status) }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        @if ($ad->status !== 'expired' && $ad->status !== 'rejected')
                            <a href="{{ route('vendor.advertisements.edit', $ad->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Edit Advertisement
                            </a>
                        @endif
                        @if ($ad->isActive() && $ad->isExpiringSoon(7))
                            <button type="button" class="btn btn-success"
                                onclick="extendAdvertisement({{ $ad->id }})">
                                <i class="fas fa-clock me-2"></i>Extend Campaign
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Create Advertisement Modal -->
    <div class="modal fade" id="createAdModal" tabindex="-1" aria-labelledby="createAdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-primary text-white border-0">
                    <div>
                        <h4 class="modal-title mb-0" id="createAdModalLabel">
                            <i class="fas fa-bullhorn me-2"></i>Create New Advertisement Campaign
                        </h4>
                        <p class="mb-0 text-white-50 small">Choose the perfect package for your advertising needs</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Header Info -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-light border-left-primary">
                                <div class="d-flex align-items-center">
                                    <div class="alert-icon me-3">
                                        <i class="fas fa-info-circle text-primary fa-lg"></i>
                                    </div>
                                    <div>
                                        <strong>Getting Started</strong>
                                        <p class="mb-0 text-muted small">Select an advertisement package below to begin
                                            your campaign. Each package offers different visibility options and durations.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Package Selection Grid -->
                    <div class="row g-4">
                        @forelse($packages as $package)
                            <div class="col-lg-4 col-md-6">
                                <div class="package-card card h-100 border-0 shadow-sm package-selector {{ !$package->isAvailable() ? 'package-unavailable' : '' }}"
                                    data-package-id="{{ $package->id }}"
                                    style="{{ !$package->isAvailable() ? 'opacity: 0.6; cursor: not-allowed;' : 'cursor: pointer;' }}">

                                    <!-- Package Header -->
                                    <div class="card-header bg-white border-0 pt-4 pb-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="package-icon">
                                                <div class="icon-circle bg-light-primary">
                                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                                </div>
                                            </div>
                                            <div class="package-status">
                                                @if ($package->isAvailable())
                                                    <span class="badge bg-success rounded-pill">
                                                        <i class="fas fa-check-circle me-1"></i>Available
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger rounded-pill">
                                                        <i class="fas fa-times-circle me-1"></i>Full
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <h5 class="card-title mb-1 text-dark">{{ $package->name }}</h5>
                                        <p class="text-muted small mb-0">Premium advertising placement</p>
                                    </div>

                                    <!-- Package Details -->
                                    <div class="card-body pt-0">
                                        <div class="row g-3 mb-4">
                                            <!-- Location -->
                                            <div class="col-12">
                                                <div class="d-flex align-items-center">
                                                    <div class="feature-icon me-3">
                                                        <i class="fas fa-globe text-info"></i>
                                                    </div>
                                                    <div>
                                                        <div class="small text-muted">Location</div>
                                                        <div class="fw-semibold">{{ $package->location_display }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Duration -->
                                            <div class="col-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="feature-icon me-3">
                                                        <i class="fas fa-calendar-alt text-warning"></i>
                                                    </div>
                                                    <div>
                                                        <div class="small text-muted">Duration</div>
                                                        <div class="fw-semibold">{{ $package->duration_days }} days</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Dimensions (if available) -->
                                            <div class="col-6">
                                                <div class="d-flex align-items-center">
                                                    <div class="feature-icon me-3">
                                                        <i class="fas fa-expand-arrows-alt text-success"></i>
                                                    </div>
                                                    <div>
                                                        <div class="small text-muted">Size</div>
                                                        <div class="fw-semibold">
                                                            @if (isset($package->dimensions))
                                                                {{ $package->dimensions }}
                                                            @else
                                                                Standard
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Available Slots -->
                                            <div class="col-12">
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="feature-icon me-3">
                                                        <i class="fas fa-users text-purple"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <div class="small text-muted">Available Slots</div>
                                                                <div class="fw-semibold">
                                                                    {{ $package->available_slots }}/{{ $package->max_slots }}
                                                                </div>
                                                            </div>
                                                            <div class="small text-muted">
                                                                {{ round(($package->available_slots / $package->max_slots) * 100) }}%
                                                                available
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Progress Bar -->
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: {{ $package->max_slots * 100 }}%">
                                                    </div>
                                                </div>
                                            </div>


                                        </div>

                                        <!-- Pricing Section -->
                                        <div class="pricing-section text-center">
                                            <div class="price-display mb-3">
                                                <span class="price-currency text-muted">₦</span>
                                                <span
                                                    class="price-amount display-6 fw-bold text-primary">{{ number_format($package->price, 0) }}</span>
                                                <div class="price-period small text-muted">for
                                                    {{ $package->duration_days }} days</div>
                                            </div>

                                            <!-- Selection Indicator -->
                                            <div class="selection-indicator d-none">
                                                <div class="selected-badge">
                                                    <i class="fas fa-check-circle text-success me-1"></i>
                                                    <span class="small fw-semibold text-success">Selected</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Package Footer -->
                                    <div class="card-footer bg-light border-0">
                                        <div class="d-grid">
                                            @if ($package->isAvailable())
                                                <button type="button"
                                                    class="btn btn-outline-primary btn-sm select-package-btn">
                                                    <i class="fas fa-mouse-pointer me-2"></i>Select Package
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-secondary btn-sm" disabled>
                                                    <i class="fas fa-ban me-2"></i>Unavailable
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Selected Overlay -->
                                    <div class="selected-overlay">
                                        <div class="selected-checkmark">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="empty-state text-center py-5">
                                    <div class="empty-icon mb-3">
                                        <i class="fas fa-box-open fa-4x text-muted"></i>
                                    </div>
                                    <h5 class="text-muted mb-2">No Packages Available</h5>
                                    <p class="text-muted">Advertisement packages are currently being prepared. Please check
                                        back later.</p>
                                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                    </button>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-light border-0">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="footer-info">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Secure payment processing & 24/7 support
                            </small>
                        </div>
                        <div class="footer-actions">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="button" class="btn btn-primary" id="proceedBtn" onclick="proceedToCreate()"
                                disabled>
                                <i class="fas fa-arrow-right me-2"></i>Continue to Create Ad
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection



@section('css')
    <style>
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .package-selector {
            cursor: pointer;
            transition: all 0.2s;
        }

        .package-selector:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .x-small {
            font-size: 0.7rem;
        }
    </style>
    <style>
        /* Modal Enhancements */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .border-left-primary {
            border-left: 4px solid #4e73df !important;
        }

        /* Package Cards */
        .package-card {
            transition: all 0.3s ease;
            border-radius: 12px !important;
            overflow: hidden;
            position: relative;
        }

        .package-card:hover:not(.package-unavailable) {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .package-card.selected {
            border: 2px solid #4e73df !important;
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(78, 115, 223, 0.25) !important;
        }

        .package-card.selected .selected-overlay {
            opacity: 1;
            visibility: visible;
        }

        .package-card.selected .selection-indicator {
            display: block !important;
        }

        .package-card.selected .select-package-btn {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
        }

        /* Icon Styling */
        .icon-circle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .bg-light-primary {
            background-color: rgba(78, 115, 223, 0.1) !important;
        }

        .feature-icon {
            width: 20px;
            text-align: center;
            font-size: 0.9rem;
        }

        .text-purple {
            color: #6f42c1 !important;
        }

        /* Progress Bar */
        .progress-sm {
            height: 4px;
            border-radius: 2px;
        }

        /* Pricing Display */
        .price-display {
            position: relative;
        }

        .price-currency {
            font-size: 1.2rem;
            vertical-align: top;
            margin-right: 2px;
        }

        .price-amount {
            line-height: 1;
            font-weight: 700 !important;
        }

        .price-period {
            margin-top: -5px;
        }

        /* Selected Overlay */
        .selected-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(78, 115, 223, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .selected-checkmark {
            background: rgba(78, 115, 223, 0.9);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            animation: checkmarkPulse 0.6s ease;
        }

        @keyframes checkmarkPulse {
            0% {
                transform: scale(0);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Badge Enhancements */
        .badge.rounded-pill {
            font-size: 0.75rem;
            padding: 0.4em 0.8em;
        }

        /* Empty State */
        .empty-state .empty-icon {
            opacity: 0.6;
        }

        /* Button Enhancements */
        .btn {
            font-weight: 500;
            letter-spacing: 0.025em;
        }

        .select-package-btn {
            transition: all 0.2s ease;
        }

        /* Alert Enhancements */
        .alert-light {
            background-color: #f8f9fc;
            border-color: #e3e6f0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .modal-xl {
                margin: 0.5rem;
            }

            .package-card {
                margin-bottom: 1rem;
            }

            .modal-footer {
                flex-direction: column;
                gap: 1rem;
            }

            .footer-info,
            .footer-actions {
                width: 100%;
                text-align: center;
            }
        }
    </style>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all modals
            var modalElements = document.querySelectorAll('.modal');
            modalElements.forEach(function(modalElement) {
                new bootstrap.Modal(modalElement);
            });

            // Display success/error messages from session
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4e73df'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4e73df'
                });
            @endif
        });

        // Package selection functionality
        let selectedPackageId = null;
        const proceedBtn = document.getElementById('proceedBtn');

        // Package selector click handlers
        document.querySelectorAll('.package-selector:not(.package-unavailable)').forEach(card => {
            card.addEventListener('click', function() {
                // Remove selection from all cards
                document.querySelectorAll('.package-selector').forEach(c => {
                    c.classList.remove('selected');
                    const btn = c.querySelector('.select-package-btn');
                    if (btn) {
                        btn.innerHTML = '<i class="fas fa-mouse-pointer me-2"></i>Select Package';
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-outline-primary');
                    }
                });

                // Add selection to clicked card
                this.classList.add('selected');
                const selectedBtn = this.querySelector('.select-package-btn');
                if (selectedBtn) {
                    selectedBtn.innerHTML = '<i class="fas fa-check me-2"></i>Selected';
                    selectedBtn.classList.remove('btn-outline-primary');
                    selectedBtn.classList.add('btn-primary');
                }

                // Update selected package ID
                selectedPackageId = this.dataset.packageId;

                // Enable proceed button
                proceedBtn.disabled = false;
                proceedBtn.classList.remove('btn-secondary');
                proceedBtn.classList.add('btn-primary');
            });
        });

        // Global function for proceed button
        window.proceedToCreate = function() {
            if (!selectedPackageId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please select a package first',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4e73df'
                });
                return;
            }

            // Add loading state
            proceedBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
            proceedBtn.disabled = true;

            // Redirect to subscription form
            window.location.href = '{{ route('vendor.advertisements.subscribe', '') }}/' + selectedPackageId;
        };

        // Filter functionality
        document.getElementById('statusFilter').addEventListener('change', function() {
            const filter = this.value;
            const rows = document.querySelectorAll('#dataTable tbody tr');

            rows.forEach(row => {
                if (filter === '' || row.dataset.status === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Subscribe to package
        function subscribeToPackage(packageId) {
            selectedPackageId = packageId;
            // Close any open modals
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(modal => {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) modalInstance.hide();
            });
            // Redirect to subscription form with package pre-selected
            window.location.href = '{{ route('vendor.advertisements.subscribe', '') }}/' + packageId;
        }

        // Extend advertisement
        function extendAdvertisement(adId) {
            Swal.fire({
                title: 'Extend Campaign',
                text: 'Do you want to extend this advertisement campaign?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Extend',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ url('vendor/advertisements') }}/' + adId + '/extend';
                }
            });
        }

        // Cancel advertisement
        function cancelAdvertisement(adId, event) {
            event.preventDefault();
            Swal.fire({
                title: 'Cancel Advertisement',
                text: 'Are you sure you want to cancel this advertisement? This action may be eligible for a refund if within 24 hours.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Cancel',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = event.currentTarget.getAttribute('href');
                }
            });
            return false;
        }

        // Delete advertisement
        function deleteAdvertisement(adId) {
            Swal.fire({
                title: 'Delete Advertisement',
                text: 'Are you sure you want to delete this advertisement? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#4e73df',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('vendor.advertisements.destroy', ':id') }}'.replace(':id', adId);
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        // Refresh table
        function refreshTable() {
            Swal.fire({
                title: 'Refreshing',
                text: 'Reloading advertisement data...',
                icon: 'info',
                showConfirmButton: false,
                timer: 1500,
                willClose: () => {
                    location.reload();
                }
            });
        }

        // Auto-refresh every 5 minutes for stats
        setInterval(() => {
            fetch(window.location.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update stats if endpoint returns JSON
                    console.log('Stats refreshed');
                })
                .catch(error => console.log('Auto-refresh skipped'));
        }, 300000);
    </script>
@endsection
