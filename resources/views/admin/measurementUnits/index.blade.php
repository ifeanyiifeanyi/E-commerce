{{-- resources/views/admin/measurement-units/index.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Measurement Units')

@section('breadcrumb-parent', 'Dashboard')
@section('breadcrumb-parent-route', route('admin.dashboard'))
@section('breadcrumb-current', 'Measurement Units')

@section('admin-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Measurement Units</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.measurement-units.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add New Unit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="datatable">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Name</th>
                                    <th>Symbol</th>
                                    <th>Type</th>
                                    <th>Base Unit</th>
                                    <th>Conversion Factor</th>
                                    <th>Status</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($measurementUnits as $unit)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $unit->name }}</td>
                                        <td>{{ $unit->symbol }}</td>
                                        <td>{{ $types[$unit->type] ?? $unit->type }}</td>
                                        <td>
                                            @if ($unit->is_base_unit)
                                                <span class="badge bg-info">Base Unit</span>
                                            @else
                                                {{ $unit->baseUnit->formatted_name ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td>{{ $unit->conversion_factor }}</td>
                                        <td>
                                            @if ($unit->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.measurement-units.show', $unit) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.measurement-units.edit', $unit) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.measurement-units.destroy', $unit) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this unit?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.measurement-units.toggle-active', $unit) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $unit->is_active ? 'btn-secondary' : 'btn-success' }}">
                                                        <i class="fas {{ $unit->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No measurement units found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $measurementUnits->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .btn-group .btn {
        margin-right: 2px;
    }
</style>
@endsection

@section('js')
<script>
    // Add any JavaScript needed for the measurement units page
</script>
@endsection
