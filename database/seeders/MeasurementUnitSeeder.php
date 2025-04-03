<?php

namespace Database\Seeders;

use App\Models\MeasurementUnit;
use Illuminate\Database\Seeder;

class MeasurementUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Weight units
        $kilogram = MeasurementUnit::create([
            'name' => 'Kilogram',
            'symbol' => 'kg',
            'type' => MeasurementUnit::TYPE_WEIGHT,
            'is_base_unit' => true,
            'conversion_factor' => 1,
        ]);



        MeasurementUnit::create([
            'name' => 'Gram',
            'symbol' => 'g',
            'type' => MeasurementUnit::TYPE_WEIGHT,
            'base_unit_id' => $kilogram->id,
            'conversion_factor' => 0.001,
        ]);

        MeasurementUnit::create([
            'name' => 'Milligram',
            'symbol' => 'mg',
            'type' => MeasurementUnit::TYPE_WEIGHT,
            'base_unit_id' => $kilogram->id,
            'conversion_factor' => 0.000001,
        ]);

        MeasurementUnit::create([
            'name' => 'Ton',
            'symbol' => 't',
            'type' => MeasurementUnit::TYPE_WEIGHT,
            'base_unit_id' => $kilogram->id,
            'conversion_factor' => 1000,
        ]);
        MeasurementUnit::create([
            'name' => 'Ounce',
            'symbol' => 'oz',
            'type' => MeasurementUnit::TYPE_WEIGHT,
            'base_unit_id' => $kilogram->id,
            'conversion_factor' => 0.0283495,
        ]);

        MeasurementUnit::create([
            'name' => 'Pound',
            'symbol' => 'lb',
            'type' => MeasurementUnit::TYPE_WEIGHT,
            'base_unit_id' => $kilogram->id,
            'conversion_factor' => 0.45359237,
        ]);

        // Volume units
        $liter = MeasurementUnit::create([
            'name' => 'Liter',
            'symbol' => 'L',
            'type' => MeasurementUnit::TYPE_VOLUME,
            'is_base_unit' => true,
            'conversion_factor' => 1,
        ]);
        MeasurementUnit::create([
            'name' => 'Deciliter',
            'symbol' => 'dl',
            'type' => MeasurementUnit::TYPE_VOLUME,
            'base_unit_id' => $liter->id,
            'conversion_factor' => 0.1,
        ]);

        MeasurementUnit::create([
            'name' => 'Centiliter',
            'symbol' => 'cl',
            'type' => MeasurementUnit::TYPE_VOLUME,
            'base_unit_id' => $liter->id,
            'conversion_factor' => 0.01,
        ]);

        

        MeasurementUnit::create([
            'name' => 'Milliliter',
            'symbol' => 'ml',
            'type' => MeasurementUnit::TYPE_VOLUME,
            'base_unit_id' => $liter->id,
            'conversion_factor' => 0.001,
        ]);

        // Count units
        $piece = MeasurementUnit::create([
            'name' => 'Piece',
            'symbol' => 'pc',
            'type' => MeasurementUnit::TYPE_COUNT,
            'is_base_unit' => true,
            'conversion_factor' => 1,
        ]);

        MeasurementUnit::create([
            'name' => 'Dozen',
            'symbol' => 'dz',
            'type' => MeasurementUnit::TYPE_COUNT,
            'base_unit_id' => $piece->id,
            'conversion_factor' => 12,
        ]);

        MeasurementUnit::create([
            'name' => 'Pair',
            'symbol' => 'pr',
            'type' => MeasurementUnit::TYPE_COUNT,
            'base_unit_id' => $piece->id,
            'conversion_factor' => 2,
        ]);

        // Length units
        $meter = MeasurementUnit::create([
            'name' => 'Meter',
            'symbol' => 'm',
            'type' => MeasurementUnit::TYPE_LENGTH,
            'is_base_unit' => true,
            'conversion_factor' => 1,
        ]);

        MeasurementUnit::create([
            'name' => 'Centimeter',
            'symbol' => 'cm',
            'type' => MeasurementUnit::TYPE_LENGTH,
            'base_unit_id' => $meter->id,
            'conversion_factor' => 0.01,
        ]);

        MeasurementUnit::create([
            'name' => 'Millimeter',
            'symbol' => 'mm',
            'type' => MeasurementUnit::TYPE_LENGTH,
            'base_unit_id' => $meter->id,
            'conversion_factor' => 0.001,
        ]);
        MeasurementUnit::create([
            'name' => 'Kilometer',
            'symbol' => 'km',
            'type' => MeasurementUnit::TYPE_LENGTH,
            'base_unit_id' => $meter->id,
            'conversion_factor' => 1000,
        ]);
        MeasurementUnit::create([
            'name' => 'Inch',
            'symbol' => 'in',
            'type' => MeasurementUnit::TYPE_LENGTH,
            'base_unit_id' => $meter->id,
            'conversion_factor' => 0.0254,
        ]);

        MeasurementUnit::create([
            'name' => 'Foot',
            'symbol' => 'ft',
            'type' => MeasurementUnit::TYPE_LENGTH,
            'base_unit_id' => $meter->id,
            'conversion_factor' => 0.3048,
        ]);
        MeasurementUnit::create([
            'name' => 'Yard',
            'symbol' => 'yd',
            'type' => MeasurementUnit::TYPE_LENGTH,
            'base_unit_id' => $meter->id,
            'conversion_factor' => 0.9144,
        ]);

        // Area units
        $squareMeter = MeasurementUnit::create([
            'name' => 'Square Meter',
            'symbol' => 'm²',
            'type' => MeasurementUnit::TYPE_AREA,
            'is_base_unit' => true,
            'conversion_factor' => 1,
        ]);

        // Agricultural units
        MeasurementUnit::create([
            'name' => 'Bunch',
            'symbol' => 'bnch',
            'type' => MeasurementUnit::TYPE_COUNT,
            'base_unit_id' => $piece->id,
            'conversion_factor' => 1, // Variable, depends on product
        ]);

        // Package units
        MeasurementUnit::create([
            'name' => 'Pack',
            'symbol' => 'pk',
            'type' => MeasurementUnit::TYPE_COUNT,
            'base_unit_id' => $piece->id,
            'conversion_factor' => 1, // Variable, depends on product
        ]);

        MeasurementUnit::create([
            'name' => 'Box',
            'symbol' => 'box',
            'type' => MeasurementUnit::TYPE_COUNT,
            'base_unit_id' => $piece->id,
            'conversion_factor' => 1, // Variable, depends on product
        ]);

        // Time units
       $hour = MeasurementUnit::create([
            'name' => 'Hour',
            'symbol' => 'hr',
            'type' => MeasurementUnit::TYPE_TIME,
            'is_base_unit' => true,
            'conversion_factor' => 1,
        ]);

        MeasurementUnit::create([
            'name' => 'Minute',
            'symbol' => 'min',
            'type' => MeasurementUnit::TYPE_TIME,
            'base_unit_id' => $hour->id,
            'conversion_factor' => 1 / 60,
        ]);
        MeasurementUnit::create([
            'name' => 'Second',
            'symbol' => 's',
            'type' => MeasurementUnit::TYPE_TIME,
            'base_unit_id' => $hour->id,
            'conversion_factor' => 1 / 3600,
        ]);

        // Temperature units
        $celsius = MeasurementUnit::create([
            'name' => 'Celsius',
            'symbol' => '°C',
            'type' => MeasurementUnit::TYPE_TEMPERATURE,
            'is_base_unit' => true,
            'conversion_factor' => 1,
        ]);
        MeasurementUnit::create([
            'name' => 'Fahrenheit',
            'symbol' => '°F',
            'type' => MeasurementUnit::TYPE_TEMPERATURE,
            'base_unit_id' => $celsius->id,
            'conversion_factor' => 1.8,
        ]);
        MeasurementUnit::create([
            'name' => 'Kelvin',
            'symbol' => 'K',
            'type' => MeasurementUnit::TYPE_TEMPERATURE,
            'base_unit_id' => $celsius->id,
            'conversion_factor' => 1,
        ]);

        // Currency units
        $usd = MeasurementUnit::create([
            'name' => 'US Dollar',
            'symbol' => '$',
            'type' => MeasurementUnit::TYPE_CURRENCY,
            'is_base_unit' => true,
            'conversion_factor' => 1,
        ]);
        MeasurementUnit::create([
            'name' => 'Euro',
            'symbol' => '€',
            'type' => MeasurementUnit::TYPE_CURRENCY,
            'base_unit_id' => $usd->id,
            'conversion_factor' => 1.1, // Example conversion rate
        ]);
        MeasurementUnit::create([
            'name' => 'British Pound',
            'symbol' => '£',
            'type' => MeasurementUnit::TYPE_CURRENCY,
            'base_unit_id' => $usd->id,
            'conversion_factor' => 1.3, // Example conversion rate
        ]);



        // Add more units as needed...
    }
}
