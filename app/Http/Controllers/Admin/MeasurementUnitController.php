<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\MeasurementUnit;
use App\Http\Controllers\Controller;
use App\Services\MeasurementUnitService;
use App\Http\Requests\MeasurementUnitRequest;

class MeasurementUnitController extends Controller
{
    public function __construct(
        private MeasurementUnitService $measurementUnitService
    ) {}
    public function index()
    {
        $measurementUnits = MeasurementUnit::with('baseUnit')->latest()->simplePaginate(100);
        $types = MeasurementUnit::getTypes();
        return view('admin.measurementUnits.index', compact('measurementUnits', 'types'));
    }

    /**
     * Show the form for creating a new measurement unit.
     */
    public function create()
    {
        $types = MeasurementUnit::getTypes();
        $baseUnits = MeasurementUnit::where('is_base_unit', true)->get();

        return view('admin.measurementUnits.create', compact('types', 'baseUnits'));
    }

    /**
     * Get measurement unit details via AJAX
     */
    public function getUnitDetails(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:measurement_units,id',
        ]);

        $unit = MeasurementUnit::with('baseUnit')
            ->findOrFail($request->unit_id);

        return response()->json($unit);
    }

    /**
     * Store a newly created measurement unit in storage.
     */

    /**
     * Store a newly created measurement unit.
     */
    public function store(MeasurementUnitRequest $request)
    {
        $this->measurementUnitService->create($request->validated());

        return redirect()
            ->route('admin.measurement-units')
            ->with('success', 'Measurement unit created successfully');
    }

    /**
     * Show the form for editing the specified measurement unit.
     */

    /**
     * Show the form for editing a measurement unit.
     */
    public function edit(MeasurementUnit $measurementUnit)
    {
        $types = MeasurementUnit::getTypes();
        $baseUnits = MeasurementUnit::where('is_base_unit', true)
            ->where('id', '!=', $measurementUnit->id)
            ->get();

        return view('admin.measurementUnits.edit', compact('measurementUnit', 'types', 'baseUnits'));
    }

    /**
     * Update the specified measurement unit.
     */
    public function update(MeasurementUnitRequest $request, MeasurementUnit $measurementUnit)
    {
        $this->measurementUnitService->update($measurementUnit, $request->validated());

        return redirect()
            ->route('admin.measurement-units')
            ->with('success', 'Measurement unit updated successfully');
    }

    /**
     * Remove the specified measurement unit.
     */
    public function destroy(MeasurementUnit $measurementUnit)
    {
        // Check if unit is in use by products
        if ($measurementUnit->products()->count() > 0) {
            return redirect()
                ->route('admin.measurement-units')
                ->with('error', 'This measurement unit cannot be deleted because it is being used by one or more products.');
        }

        // Check if unit is a base unit for other units
        if ($measurementUnit->derivedUnits()->count() > 0) {
            return redirect()
                ->route('admin.measurement-units')
                ->with('error', 'This measurement unit cannot be deleted because it is a base unit for other measurement units.');
        }

        $this->measurementUnitService->delete($measurementUnit);

        return redirect()
            ->route('admin.measurement-units')
            ->with('success', 'Measurement unit deleted successfully');
    }

    /**
     * Get units by type via AJAX
     */
    public function getUnitsByType(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:' . implode(',', array_keys(MeasurementUnit::getTypes())),
        ]);

        $units = MeasurementUnit::where('type', $request->type)
            ->where('is_active', true)
            ->get(['id', 'name', 'symbol']);

        return response()->json($units);
    }

    /**
     * Toggle the active status of a measurement unit
     */
    public function toggleActive(MeasurementUnit $measurementUnit)
    {
        $this->measurementUnitService->toggleActive($measurementUnit);

        return redirect()
            ->route('admin.measurement-units')
            ->with('success', 'Measurement unit status updated successfully');
    }

    /**
     * Show measurement unit details
     */
    public function show(MeasurementUnit $measurementUnit)
    {
        $measurementUnit->load('baseUnit', 'derivedUnits', 'products');

        return view('admin.measurementUnits.show', compact('measurementUnit'));
    }
}
