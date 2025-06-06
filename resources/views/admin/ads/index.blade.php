@extends('admin.layouts.admin')

@section('title', 'Advertisement Packages')
@section('breadcrumb-parent', 'Advertisements')
@section('breadcrumb-parent-route', route('admin.advertisement.packages'))

@section('admin-content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalPackages }}</h3>
                    <p>Total Packages</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $activePackages }}</h3>
                    <p>Active Packages</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>${{ number_format($totalRevenue, 0) }}</h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $activeAdvertisements }}</h3>
                    <p>Active Ads</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ad"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Advertisement Packages</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.advertisement.packages.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create Package
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Package Details</th>
                                    <th>Location</th>
                                    <th>Pricing & Duration</th>
                                    <th>Slots Usage</th>
                                    <th>Performance</th>
                                    <th>Revenue</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $package)
                                    @php
                                        $stats = $packageStats[$package->id] ?? [];
                                        $usedSlots = $package->activeAdvertisements()->count();
                                        $slotUsagePercent = $package->max_slots > 0 ? ($usedSlots / $package->max_slots) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $package->name }}</strong>
                                                @if ($package->description)
                                                    <br><small class="text-muted">{{ Str::limit($package->description, 60) }}</small>
                                                @endif
                                                <br><small class="text-info">Created: {{ $package->created_at->format('M d, Y') }}</small>
                                                @if($package->features)
                                                    <br><small class="text-muted">
                                                        Features:
                                                        <ul class="mb-0">
                                                            @foreach($package->features as $feature)
                                                                <li>{{ $feature }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $package->location_display }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>${{ number_format($package->price, 2) }}</strong>
                                                <br><small class="text-muted">{{ $package->duration_days }} days</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="progress mb-1" style="height: 20px;">
                                                <div class="progress-bar
                                                    @if($slotUsagePercent >= 90) bg-danger
                                                    @elseif($slotUsagePercent >= 70) bg-warning
                                                    @else bg-success @endif"
                                                    style="width: {{ $slotUsagePercent }}%">
                                                    {{ $usedSlots }}/{{ $package->max_slots }}
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ number_format($slotUsagePercent, 1) }}% utilized</small>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <div><strong>{{ number_format($stats['total_impressions'] ?? 0) }}</strong></div>
                                                <small class="text-muted">Impressions</small>
                                                <div><strong>{{ number_format($stats['total_clicks'] ?? 0) }}</strong></div>
                                                <small class="text-muted">Clicks</small>
                                                @if(($stats['average_ctr'] ?? 0) > 0)
                                                    <div><span class="badge bg-secondary">{{ number_format($stats['average_ctr'], 2) }}% CTR</span></div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <strong>${{ number_format($stats['total_revenue'] ?? 0, 2) }}</strong>
                                                <br><small class="text-muted">{{ $stats['total_bookings'] ?? 0 }} bookings</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($package->is_active)
                                                <span class="badge bg-success">Active</span>
                                                @if($package->available_slots <= 0)
                                                    <br><span class="badge bg-warning bg-sm mt-1">Full</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @can('view', $package)
                                                    <a href="{{ route('admin.advertisement.packages.show', $package) }}"
                                                       class="btn btn-info" title="View">
                                                       <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('update', $package)
                                                    <a href="{{ route('admin.advertisement.packages.edit', $package) }}"
                                                       class="btn btn-warning" title="Edit">
                                                       <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                @can('delete', $package)
                                                    <button type="button" class="btn btn-danger" title="Delete"
                                                            onclick="deletePackage({{ $package->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No packages found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Package Performance Chart -->
    @if($packages->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Package Performance Overview</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($packages->take(6) as $package)
                                @php $stats = $packageStats[$package->id] ?? []; @endphp
                                <div class="col-md-4 col-sm-6 mb-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-chart-bar"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ Str::limit($package->name, 20) }}</span>
                                            <span class="info-box-number">${{ number_format($stats['total_revenue'] ?? 0) }}</span>
                                            <div class="progress">
                                                <div class="progress-bar" style="width: {{ min(100, ($stats['total_bookings'] ?? 0) * 10) }}%"></div>
                                            </div>
                                            <span class="progress-description">
                                                {{ $stats['total_bookings'] ?? 0 }} bookings, {{ $stats['active_bookings'] ?? 0 }} active
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('css')
<style>
    .small-box {
        transition: all 0.3s;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .small-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .info-box {
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.9rem;
        padding: 0.35em 0.65em;
    }
</style>
@endsection

@section('js')
<script>
    function deletePackage(packageId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/advertisement/packages/${packageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Deleted!', data.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                });
            }
        })
    }

    // Initialize DataTable
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "ordering": true,
            "info": true,
            "paging": true,
            "searching": true,
            "pageLength": 25,
            "order": [[0, "asc"]]
        });
    });
</script>
@endsection
