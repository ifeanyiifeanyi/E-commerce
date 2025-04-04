{{-- resources/views/admin/measurement-units/edit.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Edit Measurement Unit')

@section('breadcrumb-parent', 'Measurement Units')
@section('breadcrumb-parent-route', route('admin.measurement-units'))
@section('breadcrumb-current', 'Edit')

@section('admin-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-7 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Measurement Unit: {{ $measurementUnit->name }}</h3>
                </div>
                <form action="{{ route('admin.measurement-units.update', $measurementUnit) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group mb-3">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $measurementUnit->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="symbol">Symbol</label>
                            <input type="text" class="form-control @error('symbol') is-invalid @enderror" id="symbol" name="symbol" value="{{ old('symbol', $measurementUnit->symbol) }}" required>
                            @error('symbol')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="type">Type</label>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Unit Type</option>
                                @foreach ($types as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', $measurementUnit->type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_base_unit" name="is_base_unit" value="1" {{ old('is_base_unit', $measurementUnit->is_base_unit) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_base_unit">This is a base unit</label>
                            </div>
                        </div>

                        <div id="derived-unit-fields" class="{{ old('is_base_unit', $measurementUnit->is_base_unit) ? 'd-none' : '' }}">
                            <div class="form-group">
                                <label for="base_unit_id">Base Unit</label>
                                <select class="form-control @error('base_unit_id') is-invalid @enderror" id="base_unit_id" name="base_unit_id">
                                    <option value="">Select Base Unit</option>
                                    @foreach ($baseUnits as $baseUnit)
                                        <option value="{{ $baseUnit->id }}" {{ old('base_unit_id', $measurementUnit->base_unit_id) == $baseUnit->id ? 'selected' : '' }}>
                                            {{ $baseUnit->formatted_name }} ({{ $types[$baseUnit->type] ?? $baseUnit->type }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('base_unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="conversion_factor">Conversion Factor</label>
                                <input type="number" step="0.000001" class="form-control @error('conversion_factor') is-invalid @enderror" id="conversion_factor" name="conversion_factor" value="{{ old('conversion_factor', $measurementUnit->conversion_factor) }}" placeholder="e.g. 1000 for 1 kg = 1000 g">
                                @error('conversion_factor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $measurementUnit->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.measurement-units') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isBaseUnitCheckbox = document.getElementById('is_base_unit');
        const derivedUnitFields = document.getElementById('derived-unit-fields');
        const typeSelect = document.getElementById('type');
        const baseUnitSelect = document.getElementById('base_unit_id');

        isBaseUnitCheckbox.addEventListener('change', function() {
            if (this.checked) {
                derivedUnitFields.classList.add('d-none');
            } else {
                derivedUnitFields.classList.remove('d-none');
            }
        });

        typeSelect.addEventListener('change', function() {
            if (!isBaseUnitCheckbox.checked) {
                fetchBaseUnitsByType(this.value);
            }
        });

        function fetchBaseUnitsByType(type) {
            fetch(`/admin/measurement-units/by-type?type=${type}`)
                .then(response => response.json())
                .then(data => {
                    baseUnitSelect.innerHTML = '<option value="">Select Base Unit</option>';
                    data.forEach(unit => {
                        const option = document.createElement('option');
                        option.value = unit.id;
                        option.textContent = `${unit.name} (${unit.symbol})`;
                        baseUnitSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching base units:', error));
        }
    });
</script>
@endsection
