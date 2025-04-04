<?php

namespace App\Services;

use App\Models\MeasurementUnit;

class MeasurementUnitService
{
    /**
     * Create a new measurement unit
     *
     * @param array $data
     * @return MeasurementUnit
     */
    public function create(array $data)
    {
        // If this is a base unit, clear base_unit_id and set conversion_factor to 1
        if (isset($data['is_base_unit']) && $data['is_base_unit']) {
            $data['base_unit_id'] = null;
            $data['conversion_factor'] = 1;
        }

        return MeasurementUnit::create($data);
    }


    /**
     * Update an existing measurement unit
     *
     * @param MeasurementUnit $measurementUnit
     * @param array $data
     * @return bool
     */
    public function update(MeasurementUnit $measurementUnit, array $data)
    {
        // If this is a base unit, clear base_unit_id and set conversion_factor to 1
        if (isset($data['is_base_unit']) && $data['is_base_unit']) {
            $data['base_unit_id'] = null;
            $data['conversion_factor'] = 1;
        }

        return $measurementUnit->update($data);
    }

     /**
     * Delete a measurement unit
     *
     * @param MeasurementUnit $measurementUnit
     * @return bool|null
     */
    public function delete(MeasurementUnit $measurementUnit)
    {
        return $measurementUnit->delete();
    }

    /**
     * Toggle active status of a measurement unit
     *
     * @param MeasurementUnit $measurementUnit
     * @return bool
     */
    public function toggleActive(MeasurementUnit $measurementUnit)
    {
        return $measurementUnit->update([
            'is_active' => !$measurementUnit->is_active
        ]);
    }
     /**
     * Convert a value from one unit to another
     *
     * @param float $value
     * @param MeasurementUnit $fromUnit
     * @param MeasurementUnit $toUnit
     * @return float|null
     */
    public function convertValue(float $value, MeasurementUnit $fromUnit, MeasurementUnit $toUnit)
    {
        // Check if units are of the same type
        if ($fromUnit->type !== $toUnit->type) {
            return null;
        }

        // Convert to base unit first
        $valueInBaseUnit = $value * $fromUnit->conversion_factor;

        // Then convert from base unit to target unit
        return $valueInBaseUnit / $toUnit->conversion_factor;
    }
}
