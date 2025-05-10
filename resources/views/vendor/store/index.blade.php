@extends('vendor.layouts.vendor')

@section('title', 'Store Setup')

@section('vendor')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @isset($store)
                    <a href="{{ route('vendor.stores.show') }}" class="btn btn-primary float-right"><i class="fas fa-desktop"></i> Store Details</a>
                @endisset

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Store Setup</h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (isset($store) && $store->isRejected())
                            <div class="alert alert-danger">
                                <h5>Your store was rejected</h5>
                                <p><strong>Reason:</strong> {{ $store->rejection_reason }}</p>
                                <p>Please update your information below and submit again.</p>
                            </div>
                        @endif

                        <form action="{{ route('vendor.stores.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>Basic Information</h5>
                                    <div class="form-group mb-3">
                                        <label for="store_name">Store Name *</label>
                                        <input type="text" name="store_name" id="store_name"
                                            class="form-control @error('store_name') is-invalid @enderror"
                                            value="{{ old('store_name', $store->store_name ?? '') }}" required>
                                        @error('store_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="store_phone">Store Phone *</label>
                                        <input type="text" name="store_phone" id="store_phone"
                                            class="form-control @error('store_phone') is-invalid @enderror"
                                            value="{{ old('store_phone', $store->store_phone ?? '') }}" required>
                                        @error('store_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="store_email">Store Email *</label>
                                        <input type="email" name="store_email" id="store_email"
                                            class="form-control @error('store_email') is-invalid @enderror"
                                            value="{{ old('store_email', $store->store_email ?? '') }}" required>
                                        @error('store_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5>Store Address</h5>
                                    <div class="form-group mb-3">
                                        <label for="store_address">Street Address *</label>
                                        <input type="text" name="store_address" id="store_address"
                                            class="form-control @error('store_address') is-invalid @enderror"
                                            value="{{ old('store_address', $store->store_address ?? '') }}" required>
                                        @error('store_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="store_city">City *</label>
                                                <input type="text" name="store_city" id="store_city"
                                                    class="form-control @error('store_city') is-invalid @enderror"
                                                    value="{{ old('store_city', $store->store_city ?? '') }}" required>
                                                @error('store_city')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="store_state">State/Province *</label>
                                                <input type="text" name="store_state" id="store_state"
                                                    class="form-control @error('store_state') is-invalid @enderror"
                                                    value="{{ old('store_state', $store->store_state ?? '') }}" required>
                                                @error('store_state')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="store_postal_code">Postal Code *</label>
                                                <input type="text" name="store_postal_code" id="store_postal_code"
                                                    class="form-control @error('store_postal_code') is-invalid @enderror"
                                                    value="{{ old('store_postal_code', $store->store_postal_code ?? '') }}"
                                                    required>
                                                @error('store_postal_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="store_country">Country *</label>
                                                <select name="store_country" id="store_country"
                                                    class="form-control @error('store_country') is-invalid @enderror"
                                                    required>

                                                    <option value="">Select Country</option>

                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->name }}"
                                                            {{ old('store_country', $store->store_country ?? '') == $country->name ? 'selected' : '' }}>
                                                            {{ $country->name }}</option>
                                                    @endforeach

                                                </select>
                                                @error('store_country')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5>Store Description</h5>
                                    <div class="form-group mb-3">
                                        <label for="store_description">Store Description *</label>
                                        <textarea name="store_description" id="store_description" rows="5"
                                            class="form-control @error('store_description') is-invalid @enderror" required>{{ old('store_description', $store->store_description ?? '') }}</textarea>
                                        @error('store_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>Store Branding</h5>
                                    <div class="form-group mb-3">
                                        <label for="store_logo">Store Logo</label>
                                        <input type="file" name="store_logo" id="store_logo"
                                            class="form-control @error('store_logo') is-invalid @enderror">
                                        @error('store_logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if (isset($store) && $store->store_logo)
                                            <div class="mt-2">
                                                <img src="{{ asset($store->store_logo) }}" alt="Store Logo"
                                                    class="img-thumbnail" style="max-width: 150px;">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="store_banner">Store Banner</label>
                                        <input type="file" name="store_banner" id="store_banner"
                                            class="form-control @error('store_banner') is-invalid @enderror">
                                        @error('store_banner')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if (isset($store) && $store->store_banner)
                                            <div class="mt-2">
                                                <img src="{{ asset($store->store_banner) }}" alt="Store Banner"
                                                    class="img-thumbnail" style="max-width: 300px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5>Social Media</h5>
                                    <div class="form-group mb-3">
                                        <label for="social_facebook">Facebook URL</label>
                                        <input type="url" name="social_facebook" id="social_facebook"
                                            class="form-control @error('social_facebook') is-invalid @enderror"
                                            value="{{ old('social_facebook', $store->social_facebook ?? '') }}">
                                        @error('social_facebook')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="social_twitter">Twitter URL</label>
                                        <input type="url" name="social_twitter" id="social_twitter"
                                            class="form-control @error('social_twitter') is-invalid @enderror"
                                            value="{{ old('social_twitter', $store->social_twitter ?? '') }}">
                                        @error('social_twitter')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="social_instagram">Instagram URL</label>
                                        <input type="url" name="social_instagram" id="social_instagram"
                                            class="form-control @error('social_instagram') is-invalid @enderror"
                                            value="{{ old('social_instagram', $store->social_instagram ?? '') }}">
                                        @error('social_instagram')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="social_youtube">YouTube URL</label>
                                        <input type="url" name="social_youtube" id="social_youtube"
                                            class="form-control @error('social_youtube') is-invalid @enderror"
                                            value="{{ old('social_youtube', $store->social_youtube ?? '') }}">
                                        @error('social_youtube')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>Payment Information</h5>
                                    <div class="form-group mb-3">
                                        <label for="tax_number">Tax/VAT Number</label>
                                        <input type="text" name="tax_number" id="tax_number"
                                            class="form-control @error('tax_number') is-invalid @enderror"
                                            value="{{ old('tax_number', $store->tax_number ?? '') }}">
                                        @error('tax_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="bank_name">Bank Name</label>
                                        <input type="text" name="bank_name" id="bank_name"
                                            class="form-control @error('bank_name') is-invalid @enderror"
                                            value="{{ old('bank_name', $store->bank_name ?? '') }}">
                                        @error('bank_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="bank_account_name">Account Holder Name</label>
                                        <input type="text" name="bank_account_name" id="bank_account_name"
                                            class="form-control @error('bank_account_name') is-invalid @enderror"
                                            value="{{ old('bank_account_name', $store->bank_account_name ?? '') }}">
                                        @error('bank_account_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="bank_account_number">Account Number</label>
                                        <input type="text" name="bank_account_number" id="bank_account_number"
                                            class="form-control @error('bank_account_number') is-invalid @enderror"
                                            value="{{ old('bank_account_number', $store->bank_account_number ?? '') }}">
                                        @error('bank_account_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="bank_routing_number">Routing Number</label>
                                        <input type="text" name="bank_routing_number" id="bank_routing_number"
                                            class="form-control @error('bank_routing_number') is-invalid @enderror"
                                            value="{{ old('bank_routing_number', $store->bank_routing_number ?? '') }}">
                                        @error('bank_routing_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5>Documents</h5>
                                    <div class="alert alert-info">
                                        <p>Please make sure you have uploaded all required documents:</p>
                                        <ul>
                                            @if ($documents->isEmpty())
                                                <li>No documents uploaded yet. <a
                                                        href="{{ route('vendor.documents') }}">Upload documents</a></li>
                                            @else
                                                @foreach ($documents as $document)
                                                    <li>
                                                        {{ ucfirst($document->document_type) }}:
                                                        <span
                                                            class="badge bg-{{ $document->status == 'approved' ? 'success' : ($document->status == 'rejected' ? 'danger' : 'warning') }}">
                                                            {{ ucfirst($document->status) }}
                                                        </span>
                                                        @if ($document->status == 'rejected')
                                                            <small
                                                                class="text-danger d-block">{{ $document->rejection_reason }}</small>
                                                        @endif
                                                    </li>
                                                @endforeach
                                                <li><a href="{{ route('vendor.documents') }}">Manage documents</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5>SEO Information</h5>
                                    <div class="form-group mb-3">
                                        <label for="meta_title">Meta Title</label>
                                        <input type="text" name="meta_title" id="meta_title"
                                            class="form-control @error('meta_title') is-invalid @enderror"
                                            value="{{ old('meta_title', $store->meta_title ?? '') }}">
                                        @error('meta_title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="meta_description">Meta Description</label>
                                        <textarea name="meta_description" id="meta_description" rows="3"
                                            class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $store->meta_description ?? '') }}</textarea>
                                        @error('meta_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="meta_keywords">Meta Keywords (comma separated)</label>
                                        <input type="text" name="meta_keywords" id="meta_keywords"
                                            class="form-control @error('meta_keywords') is-invalid @enderror"
                                            value="{{ old('meta_keywords', $store->meta_keywords ?? '') }}">
                                        @error('meta_keywords')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Submit Store Details</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <!-- Additional CSS -->
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Add text editor for description if needed
            // $('#store_description').summernote({
            //     height: 200
            // });
        });
    </script>
@endsection
