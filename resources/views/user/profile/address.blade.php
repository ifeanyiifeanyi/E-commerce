{{-- resources/views/user/profile/addresses.blade.php --}}
@extends('user.layouts.customer')

@section('title', 'My Addresses')
@section('page-title', 'My Addresses')

@section('customer')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Saved Addresses</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="fas fa-plus me-2"></i>Add New Address
                </button>
            </div>
            <div class="card-body">
                @if($addresses->count() > 0)
                    <div class="row">
                        @foreach($addresses as $address)
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <div class="card border {{ $address->is_default ? 'border-primary' : '' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h6 class="card-title mb-1">
                                                    {{ $address->first_name }} {{ $address->last_name }}
                                                    @if($address->is_default)
                                                        <span class="badge bg-primary ms-2">Default</span>
                                                    @endif
                                                </h6>
                                                <span class="badge bg-{{ $address->address_type === 'billing' ? 'info' : ($address->address_type === 'shipping' ? 'success' : 'warning') }}">
                                                    {{ ucfirst($address->address_type) }}
                                                </span>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <button class="dropdown-item" onclick="editAddress({{ $address->id }})">
                                                            <i class="fas fa-edit me-2"></i>Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('user.addresses.delete', $address->id) }}"
                                                              onsubmit="return confirm('Are you sure you want to delete this address?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="fas fa-trash me-2"></i>Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <p class="card-text mb-1">{{ $address->address_line1 }}</p>
                                        @if($address->address_line2)
                                            <p class="card-text mb-1">{{ $address->address_line2 }}</p>
                                        @endif
                                        <p class="card-text mb-1">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                        <p class="card-text mb-2">{{ $address->country }}</p>

                                        @if($address->phone)
                                            <p class="card-text text-muted mb-0">
                                                <i class="fas fa-phone me-2"></i>{{ $address->phone }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <h5>No addresses saved yet</h5>
                        <p class="text-muted">Add your first address to get started</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            <i class="fas fa-plus me-2"></i>Add Address
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('user.addresses.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Address Type *</label>
                            <select name="address_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="billing">Billing</option>
                                <option value="shipping">Shipping</option>
                                <option value="both">Both</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_default" value="1" id="is_default">
                                <label class="form-check-label" for="is_default">
                                    Set as default address
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name *</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name *</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address Line 1 *</label>
                        <input type="text" name="address_line1" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" name="address_line2" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">City *</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">State *</label>
                            <input type="text" name="state" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Postal Code *</label>
                            <input type="text" name="postal_code" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Country *</label>
                            <select name="country" class="form-select" required>
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option {{ old('country') == $country->name ? 'selected' : '' }} value="{{ $country->name }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Address Modal -->
<div class="modal fade" id="editAddressModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAddressForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body" id="editAddressFormBody">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editAddress(addressId) {
    // Find the address data from the rendered addresses
    const addresses = @json($addresses);
    const address = addresses.find(addr => addr.id === addressId);
    const countries = @json($countries);

     // Build country options dynamically
    let countryOptions = '<option value="">Select Country</option>';
    countries.forEach(country => {
        const selected = address.country === country.name ? 'selected' : '';
        countryOptions += `<option value="${country.name}" ${selected}>${country.name}</option>`;
    });

    if (!address) return;

    // Update form action
    document.getElementById('editAddressForm').action = `/customer/addresses/${addressId}`;

    // Populate form fields
    const formBody = document.getElementById('editAddressFormBody');
    formBody.innerHTML = `
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Address Type *</label>
                <select name="address_type" class="form-select" required>
                    <option value="billing" ${address.address_type === 'billing' ? 'selected' : ''}>Billing</option>
                    <option value="shipping" ${address.address_type === 'shipping' ? 'selected' : ''}>Shipping</option>
                    <option value="both" ${address.address_type === 'both' ? 'selected' : ''}>Both</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="is_default" value="1" ${address.is_default ? 'checked' : ''}>
                    <label class="form-check-label">Set as default address</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">First Name *</label>
                <input type="text" name="first_name" class="form-control" value="${address.first_name}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name" class="form-control" value="${address.last_name}" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Address Line 1 *</label>
            <input type="text" name="address_line1" class="form-control" value="${address.address_line1}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Address Line 2</label>
            <input type="text" name="address_line2" class="form-control" value="${address.address_line2 || ''}">
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">City *</label>
                <input type="text" name="city" class="form-control" value="${address.city}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">State *</label>
                <input type="text" name="state" class="form-control" value="${address.state}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Postal Code *</label>
                <input type="text" name="postal_code" class="form-control" value="${address.postal_code}" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Country *</label>
               <select name="country" class="form-select" required>
                    ${countryOptions}
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Phone</label>
                <input type="tel" name="phone" class="form-control" value="${address.phone || ''}">
            </div>
        </div>
    `;

    // Show modal
    new bootstrap.Modal(document.getElementById('editAddressModal')).show();
}
</script>
@endpush
