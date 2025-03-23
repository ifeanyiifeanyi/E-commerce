@extends('vendor.layouts.vendor')

@section('title', 'Store Details')

@section('vendor')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Store Details</h4>
                    <a href="{{ route('vendor.stores') }}" class="btn btn-primary">Edit Store</a>
                </div>
                <div class="card-body">
                    @if($store->status == 'pending')
                        <div class="alert alert-info">
                            <h5>Your store is under review</h5>
                            <p>We'll notify you once the review is complete.</p>
                        </div>
                    @elseif($store->status == 'rejected')
                        <div class="alert alert-danger">
                            <h5>Your store was rejected</h5>
                            <p><strong>Reason:</strong> {{ $store->rejection_reason }}</p>
                            <p>Please update your information and submit again.</p>
                        </div>
                    @elseif($store->status == 'approved')
                        <div class="alert alert-success">
                            <h5>Your store is approved</h5>
                            <p>You can now add products and start selling.</p>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Basic Information</h5>
                            <table class="table">
                                <tr>
                                    <th width="40%">Store Name</th>
                                    <td>{{ $store->store_name }}</td>
                                </tr>
                                <tr>
                                    <th>Store Phone</th>
                                    <td>{{ $store->store_phone }}</td>
                                </tr>
                                <tr>
                                    <th>Store Email</th>
                                    <td>{{ $store->store_email }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Store Address</h5>
                            <table class="table">
                                <tr>
                                    <th width="40%">Street Address</th>
                                    <td>{{ $store->store_address }}</td>
                                </tr>
                                <tr>
                                    <th>City</th>
                                    <td>{{ $store->store_city }}</td>
                                </tr>
                                <tr>
                                    <th>State/Province</th>
                                    <td>{{ $store->store_state }}</td>
                                </tr>
                                <tr>
                                    <th>Postal Code</th>
                                    <td>{{ $store->store_postal_code }}</td>
                                </tr>
                                <tr>
                                    <th>Country</th>
                                    <td>{{ $store->store_country }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>Store Description</h5>
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($store->store_description)) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Store Branding</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Store Logo</strong></p>
                                    @if($store->store_logo)
                                        <img src="{{ asset($store->store_logo) }}" alt="Store Logo" class="img-thumbnail" style="max-width: 150px;">
                                    @else
                                        <p class="text-muted">No logo uploaded</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Store Banner</strong></p>
                                    @if($store->store_banner)
                                        <img src="{{ asset($store->store_banner) }}" alt="Store Banner" class="img-thumbnail" style="max-width: 100%;">
                                    @else
                                        <p class="text-muted">No banner uploaded</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5>Social Media</h5>
                            <table class="table">
                                <tr>
                                    <th width="40%">Facebook</th>
                                    <td>
                                        @if($store->social_facebook)
                                            <a href="{{ $store->social_facebook }}" target="_blank">{{ $store->social_facebook }}</a>
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Twitter</th>
                                    <td>
                                        @if($store->social_twitter)
                                            <a href="{{ $store->social_twitter }}" target="_blank">{{ $store->social_twitter }}</a>
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Instagram</th>
                                    <td>
                                        @if($store->social_instagram)
                                            <a href="{{ $store->social_instagram }}" target="_blank">{{ $store->social_instagram }}</a>
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>YouTube</th>
                                    <td>
                                        @if($store->social_youtube)
                                            <a href="{{ $store->social_youtube }}" target="_blank">{{ $store->social_youtube }}</a>
                                        @else
                                            <span class="text-muted">Not provided</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Payment Information</h5>
                            <table class="table">
                                <tr>
                                    <th width="40%">Tax/VAT Number</th>
                                    <td>{{ $store->tax_number ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Bank Name</th>
                                    <td>{{ $store->bank_name ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Account Holder Name</th>
                                    <td>{{ $store->bank_account_name ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Account Number</th>
                                    <td>{{ $store->bank_account_number ? '****'.substr($store->bank_account_number, -4) : 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Routing Number</th>
                                    <td>{{ $store->bank_routing_number ? '****'.substr($store->bank_routing_number, -4) : 'Not provided' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Documents</h5>
                            <div class="alert alert-info">
                                <p>Verification Documents:</p>
                                <ul>
                                    @if($documents->isEmpty())
                                        <li>No documents uploaded yet.</li>
                                    @else
                                        @foreach($documents as $document)
                                            <li>
                                                {{ ucfirst($document->document_type) }}:
                                                <span class="badge bg-{{ $document->status == 'approved' ? 'success' : ($document->status == 'rejected' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($document->status) }}
                                                </span>
                                                @if($document->status == 'rejected')
                                                    <small class="text-danger d-block">{{ $document->rejection_reason }}</small>
                                                @endif
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                                <a href="{{ route('vendor.documents') }}" class="btn btn-sm btn-primary">Manage Documents</a>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>SEO Information</h5>
                            <table class="table">
                                <tr>
                                    <th width="20%">Meta Title</th>
                                    <td>{{ $store->meta_title ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Meta Description</th>
                                    <td>{{ $store->meta_description ?? 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <th>Meta Keywords</th>
                                    <td>{{ $store->meta_keywords ?? 'Not provided' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<!-- Additional CSS -->
<style>
    .table th {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('js')
<!-- Additional JS -->
@endsection