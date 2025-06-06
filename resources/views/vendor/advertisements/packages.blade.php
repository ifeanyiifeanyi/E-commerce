@extends('vendor.layouts.vendor')

@section('title', 'Advertisement Packages')

@section('vendor')
    <div class="container-fluid px-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800">Advertisement Packages</h1>
                <p class="text-muted">Choose the perfect package for your advertising needs</p>
            </div>
            <div>
                <a href="{{ route('vendor.advertisement') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Packages Grid -->
        <div class="row">
            @forelse($packages as $package)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div
                        class="card h-100 {{ $package->isAvailable() ? 'border-success' : 'border-secondary' }} package-card">
                        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                            <h5 class="mb-0 font-weight-bold">{{ $package->name }}</h5>
                            @if ($package->isAvailable())
                                <span class="badge bg-success">Available</span>
                            @else
                                <span class="badge bg-secondary">Fully Booked</span>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">
                            <div class="mb-3">
                                <h3 class="text-primary mb-0">₦{{ number_format($package->price, 2) }}</h3>
                                <small class="text-muted">for {{ $package->duration_days }} days</small>
                            </div>


                            <div class="mb-3">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h6 class="mb-0">{{ $package->location_display }}</h6>
                                            <small class="text-muted">Location</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="mb-0">{{ $package->available_slots }}/{{ $package->max_slots }}</h6>
                                        <small class="text-muted">Available</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: {{ ($package->activeAdvertisements->count() / $package->max_slots) * 100 }}%">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $package->activeAdvertisements->count() }} active
                                    campaigns</small>
                            </div>



                            <div class="mt-auto">
                                <div class="text-center">
                                    <button style="border: 2px solid purple; color: purple;" class="btn btn-sm"
                                        onclick="viewPackageDetails({{ $package->id }})">
                                        <i class="fas fa-info-circle me-1"></i>View Details
                                    </button>
                                    @if ($package->isAvailable())
                                        <a href="{{ route('vendor.advertisements.subscribe', $package->id) }}"
                                            class="btn btn-sm" style="background: purple; color: white;">
                                            <i class="fas fa-rocket me-2"></i>Subscribe Now
                                        </a>
                                    @else
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-ban me-2"></i>Not Available
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">No Packages Available</h4>
                        <p class="text-muted">Check back later for new advertisement packages.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Package Details Modal -->
    <div class="modal fade" id="packageDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="packageDetailsTitle">Package Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="packageDetailsBody">
                    <!-- Package details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="subscribeFromDetails">Subscribe to Package</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Package details modal
        async function viewPackageDetails(packageId) {
            try {
                const response = await fetch(`/vendor/packages/${packageId}`);
                const package = await response.json();

                document.getElementById('packageDetailsTitle').textContent = package.name;
                document.getElementById('packageDetailsBody').innerHTML = `
            <div class="row">
                <div class="col-md-8">
                    <h6>Package Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Name:</strong></td><td>${package.name}</td></tr>
                        <tr><td><strong>Location:</strong></td><td>${package.location_display}</td></tr>
                        <tr><td><strong>Duration:</strong></td><td>${package.duration_days} days</td></tr>
                        <tr><td><strong>Price:</strong></td><td>₦${Number(package.price).toLocaleString()}</td></tr>
                        <tr><td><strong>Available Slots:</strong></td><td>${package.available_slots}/${package.max_slots}</td></tr>
                    </table>
                    <h6>Description</h6>
                    <p>${package.description}</p>
                    ${package.features ? `
                            <h6>Features</h6>
                            <ul>
                                ${package.features.map(feature => `<li><i class="fas fa-check text-success me-2"></i>${feature}</li>`).join('')}
                            </ul>
                        ` : ''}
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4 class="text-primary">₦${Number(package.price).toLocaleString()}</h4>
                            <p class="text-muted">${package.duration_days} days campaign</p>
                            <div class="progress mb-3">
                                <div class="progress-bar bg-info" style="width: ${(package.active_count / package.max_slots) * 100}%"></div>
                            </div>
                            <small class="text-muted">${package.active_count} of ${package.max_slots} slots used</small>
                        </div>
                    </div>
                </div>
            </div>
        `;

                const subscribeBtn = document.getElementById('subscribeFromDetails');
                subscribeBtn.onclick = () => window.location.href =
                    `/vendor/advertisements/packages/${packageId}/subscribe`;
                subscribeBtn.style.display = package.is_available ? 'inline-block' : 'none';

                new bootstrap.Modal(document.getElementById('packageDetailsModal')).show();
            } catch (error) {
                Swal.fire('Error', 'Failed to load package details', 'error');
            }
        }
    </script>

    <style>
        .package-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
@endsection
