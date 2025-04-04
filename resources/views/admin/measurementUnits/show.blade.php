{{-- resources/views/admin/measurement-units/show.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Measurement Unit Details')

@section('breadcrumb-parent', 'Measurement Units')
@section('breadcrumb-parent-route', route('admin.measurement-units'))
@section('breadcrumb-current', 'View')

@section('admin-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Measurement Unit Details: {{ $measurementUnit->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.measurement-units.edit', $measurementUnit) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.measurement-units') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Basic Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Name</th>
                                    <td>{{ $measurementUnit->name }}</td>
                                </tr>
                                <tr>
                                    <th>Symbol</th>
                                    <td>{{ $measurementUnit->symbol }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ App\Models\MeasurementUnit::TYPE_UNITS_LABELS[$measurementUnit->type] ?? $measurementUnit->type }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($measurementUnit->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Base Unit</th>
                                    <td>
                                        @if ($measurementUnit->is_base_unit)
                                            <span class="badge bg-info">This is a base unit</span>
                                        @else
                                            <a href="{{ route('admin.measurement-units.show', $measurementUnit->baseUnit) }}">
                                                {{ $measurementUnit->baseUnit->formatted_name ?? 'N/A' }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Conversion Factor</th>
                                    <td>{{ $measurementUnit->conversion_factor }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $measurementUnit->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $measurementUnit->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            @if ($measurementUnit->is_base_unit && $measurementUnit->derivedUnits->count() > 0)
                                <h4>Derived Units</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Symbol</th>
                                                <th>Conversion Factor</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($measurementUnit->derivedUnits as $derivedUnit)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('admin.measurement-units.show', $derivedUnit) }}">
                                                            {{ $derivedUnit->name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $derivedUnit->symbol }}</td>
                                                    <td>{{ $derivedUnit->conversion_factor }}</td>
                                                    <td>
                                                        @if ($derivedUnit->is_active)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            @if ($measurementUnit->products->count() > 0)
                                <h4 class="mt-4">Associated Products</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Product Name</th>
                                                <th>SKU</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($measurementUnit->products as $product)
                                                <tr>
                                                    <td>{{ $product->id }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.products.show', $product) }}">
                                                            {{ $product->name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $product->product_code }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
