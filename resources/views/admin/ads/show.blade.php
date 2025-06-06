@extends('admin.layouts.admin')

@section('title', 'Package Details - ' . $package->name)
@section('breadcrumb-parent', 'Advertisement Packages')
@section('breadcrumb-parent-route', route('admin.advertisement.packages'))

@section('admin-content')
    <!-- Package Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-0">{{ $package->name }}</h3>
                            <small class="text-muted">Created on {{ $package->created_at->format('M d, Y \a\t H:i A') }}</small>
                        </div>
                        <div>
                            @can('update', $package)
                                <a href="{{ route('admin.advertisement.packages.edit', $package) }}"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit Package
                                </a>
                            @endcan
                            @can('delete', $package)
                                <button type="button" class="btn btn-danger btn-sm ml-2"
                                        onclick="deletePackage({{ $package->id }})">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Package Overview -->
    <div class="row mb-4">
        <!-- Package Info -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Package Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Package Name:</th>
                                    <td>{{ $package->name }}</td>
                                </tr>
                                <tr>
                                    <th>Slug:</th>
                                    <td><code>{{ $package->slug }}</code></td>
                                </tr>
                                <tr>
                                    <th>Location:</th>
                                    <td>
                                        <span class="badge bg-info">{{ $package->location_display }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Price:</th>
                                    <td><strong class="text-success">${{ number_format($package->price, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Duration:</th>
                                    <td>{{ $package->duration_days }} days</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Max Slots:</th>
                                    <td>{{ $package->max_slots }}</td>
                                </tr>
                                <tr>
                                    <th>Available Slots:</th>
                                    <td>
                                        <span class="badge {{ $package->available_slots > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $package->available_slots }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($package->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Sort Order:</th>
                                    <td>{{ $package->sort_order }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated:</th>
                                    <td>{{ $package->updated_at->format('M d, Y \a\t H:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($package->description)
                        <div class="mt-3">
                            <h6>Description:</h6>
                            <p class="text-muted">{{ $package->description }}</p>
                        </div>
                    @endif

                    @if($package->features && count($package->features) > 0)
                        <div class="mt-3">
                            <h6>Features:</h6>
                            <ul class="list-unstyled" >
                                @foreach($package->features as $feature)
                                    <li><i class="fas fa-check text-success mr-2 fa-fw"></i>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Statistics</h3>
                </div>
                <div class="card-body">
                    @php
                        $totalAds = $package->advertisements()->count();
                        $activeAds = $package->activeAdvertisements()->count();
                        $totalRevenue = $package->advertisements()->sum('amount_paid');
                        $slotUsage = $package->max_slots > 0 ? ($activeAds / $package->max_slots) * 100 : 0;
                    @endphp

                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-info p-2"><i class="fas fa-chart-line"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Advertisements</span>
                            <span class="info-box-number">{{ $totalAds }}</span>
                        </div>
                    </div>

                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success"><i class="fas fa-eye"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active Advertisements</span>
                            <span class="info-box-number">{{ $activeAds }}</span>
                        </div>
                    </div>

                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Revenue</span>
                            <span class="info-box-number">${{ number_format($totalRevenue, 2) }}</span>
                        </div>
                    </div>

                    <!-- Slot Usage Chart -->
                    <div class="mt-4">
                        <h6>Slot Usage</h6>
                        <div class="progress mb-2" style="height: 25px;">
                            <div class="progress-bar
                                @if($slotUsage >= 90) bg-danger
                                @elseif($slotUsage >= 70) bg-warning
                                @else bg-success @endif"
                                style="width: {{ $slotUsage }}%">
                                {{ $activeAds }}/{{ $package->max_slots }}
                            </div>
                        </div>
                        <small class="text-muted">{{ number_format($slotUsage, 1) }}% utilized</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Location Details -->
    @php
        $locationDetails = collect([
            'home_banner' => [
                'label' => 'Home Page Banner',
                'description' => 'Displayed prominently at the top of the home page',
                'recommended_size' => '1200x300 pixels',
                'dimensions' => ['width' => 1200, 'height' => 300],
                'max_file_size' => 2048
            ],
            'home_sidebar' => [
                'label' => 'Home Page Sidebar',
                'description' => 'Vertical banner in the sidebar of the home page',
                'recommended_size' => '300x600 pixels',
                'dimensions' => ['width' => 300, 'height' => 600],
                'max_file_size' => 1024
            ],
            'category_top' => [
                'label' => 'Category Page Top',
                'description' => 'Displayed at the top of category pages',
                'recommended_size' => '728x90 pixels',
                'dimensions' => ['width' => 728, 'height' => 90],
                'max_file_size' => 1024
            ],
            'product_detail' => [
                'label' => 'Product Detail Page',
                'description' => 'Displayed on product detail pages',
                'recommended_size' => '468x60 pixels',
                'dimensions' => ['width' => 468, 'height' => 60],
                'max_file_size' => 512
            ],
            'search_results' => [
                'label' => 'Search Results Page',
                'description' => 'Displayed on search results pages',
                'recommended_size' => '728x90 pixels',
                'dimensions' => ['width' => 728, 'height' => 90],
                'max_file_size' => 1024
            ]
        ])->get($package->location, []);
    @endphp

    @if($locationDetails)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Location Specifications</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>{{ $locationDetails['label'] }}</h6>
                                <p class="text-muted">{{ $locationDetails['description'] }}</p>
                            </div>
                            <div class="col-md-3">
                                <h6>Recommended Size</h6>
                                <p><strong>{{ $locationDetails['recommended_size'] }}</strong></p>
                                <small class="text-muted">
                                    {{ $locationDetails['dimensions']['width'] }}px × {{ $locationDetails['dimensions']['height'] }}px
                                </small>
                            </div>
                            <div class="col-md-3">
                                <h6>Max File Size</h6>
                                <p><strong>{{ $locationDetails['max_file_size'] }} KB</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Advertisements List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Advertisements ({{ $advertisements->total() }})</h3>
                </div>
                <div class="card-body">
                    @if($advertisements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Advertisement</th>
                                        <th>Vendor</th>
                                        <th>Status</th>
                                        <th>Amount Paid</th>
                                        <th>Duration</th>
                                        <th>Performance</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($advertisements as $ad)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($ad->image_path)
                                                        <img src="{{ $ad->image_url }}"
                                                             alt="{{ $ad->title }}"
                                                             class="img-thumbnail mr-2"
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $ad->title }}</strong>
                                                        @if($ad->description)
                                                            <br><small class="text-muted">{{ Str::limit($ad->description, 50) }}</small>
                                                        @endif
                                                        <br><small class="text-info">Created: {{ $ad->created_at->format('M d, Y') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $ad->vendor->name }}</strong>
                                                    <br><small class="text-muted">{{ $ad->vendor->email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                {!! $ad->status_badge !!}
                                                @if($ad->status === 'active')
                                                    <br><small class="text-muted">
                                                        @if($ad->isExpired())
                                                            <span class="text-danger">Expired</span>
                                                        @elseif($ad->isExpiringSoon())
                                                            <span class="text-warning">Expires in {{ $ad->days_remaining }} days</span>
                                                        @else
                                                            {{ $ad->days_remaining }} days left
                                                        @endif
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>${{ number_format($ad->amount_paid, 2) }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $ad->start_date->format('M d, Y') }}</strong>
                                                    <br><small class="text-muted">to {{ $ad->end_date->format('M d, Y') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-center">
                                                    <div><strong>{{ number_format($ad->impressions) }}</strong></div>
                                                    <small class="text-muted">Impressions</small>
                                                    <div><strong>{{ number_format($ad->clicks) }}</strong></div>
                                                    <small class="text-muted">Clicks</small>
                                                    @if($ad->ctr > 0)
                                                        <div><span class="badge bg-info">{{ $ad->ctr }}% CTR</span></div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical btn-group-sm">
                                                    <a href="#" class="btn btn-info btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($ad->status === 'pending')
                                                        <button type="button" class="btn btn-success btn-sm" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $advertisements->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-ad fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No advertisements found</h5>
                            <p class="text-muted">This package doesn't have any advertisements yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
    .info-box {
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
    }
    .card {
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
    .img-thumbnail {
        border-radius: 0.25rem;
    }
    .progress {
        border-radius: 0.5rem;
    }
</style>
@endsection

@section('js')
<script>
    function deletePackage(packageId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this! This will also affect all associated advertisements.",
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
                            window.location.href = '{{ route("admin.advertisement.packages") }}';
                        });
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                });
            }
        });
    }
</script>
@endsection
