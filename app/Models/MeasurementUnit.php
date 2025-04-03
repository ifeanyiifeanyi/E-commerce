<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasurementUnit extends Model
{

    // Common unit types as constants
    const TYPE_WEIGHT = 'weight';
    const TYPE_VOLUME = 'volume';
    const TYPE_LENGTH = 'length';
    const TYPE_COUNT = 'count';
    const TYPE_AREA = 'area';
    const TYPE_TIME = 'time';
    const TYPE_TEMPERATURE = 'temperature';
    const TYPE_CURRENCY = 'currency';
    const TYPE_CUSTOM = 'custom';
    const TYPE_OTHER = 'other';
    const TYPE_UNITS = [
        self::TYPE_WEIGHT,
        self::TYPE_VOLUME,
        self::TYPE_LENGTH,
        self::TYPE_COUNT,
        self::TYPE_AREA,
        self::TYPE_TIME,
        self::TYPE_TEMPERATURE,
        self::TYPE_CURRENCY,
        self::TYPE_CUSTOM,
        self::TYPE_OTHER,
    ];
    const TYPE_UNITS_LABELS = [
        self::TYPE_WEIGHT => 'Weight',
        self::TYPE_VOLUME => 'Volume',
        self::TYPE_LENGTH => 'Length',
        self::TYPE_COUNT => 'Count',
        self::TYPE_AREA => 'Area',
        self::TYPE_TIME => 'Time',
        self::TYPE_TEMPERATURE => 'Temperature',
        self::TYPE_CURRENCY => 'Currency',
        self::TYPE_CUSTOM => 'Custom',
        self::TYPE_OTHER => 'Other',
    ];
    const TYPE_UNITS_LABELS_FLIPPED = [
        'Weight' => self::TYPE_WEIGHT,
        'Volume' => self::TYPE_VOLUME,
        'Length' => self::TYPE_LENGTH,
        'Count' => self::TYPE_COUNT,
        'Area' => self::TYPE_AREA,
        'Time' => self::TYPE_TIME,
        'Temperature' => self::TYPE_TEMPERATURE,
        'Currency' => self::TYPE_CURRENCY,
        'Custom' => self::TYPE_CUSTOM,
        'Other' => self::TYPE_OTHER,
    ];
    const TYPE_UNITS_LABELS_FLIPPED_CUSTOM = [
        'Weight' => self::TYPE_WEIGHT,
        'Volume' => self::TYPE_VOLUME,
        'Length' => self::TYPE_LENGTH,
        'Count' => self::TYPE_COUNT,
        'Area' => self::TYPE_AREA,
        'Time' => self::TYPE_TIME,
        'Temperature' => self::TYPE_TEMPERATURE,
        'Currency' => self::TYPE_CURRENCY,
        'Custom' => self::TYPE_CUSTOM,
        'Other' => self::TYPE_OTHER,
    ];





    protected $fillable = [
        'name',        // e.g., kilogram, gram, pound, ounce, piece, dozen, pack, etc.
        'symbol',      // e.g., kg, g, lb, oz, pc, dz, pk, etc.
        'type',        // weight, count, volume, length, etc.
        'base_unit_id', // Reference to a base unit (e.g., gram is base unit for kg)
        'conversion_factor', // Factor to convert to base unit
        'is_base_unit', // Boolean to identify if this is a base unit
        'is_active',   // Boolean to enable/disable the unit
    ];


    protected $casts = [
        'is_base_unit' => 'boolean',
        'is_active' => 'boolean',
        'conversion_factor' => 'float',
    ];

    // Get all types as array
    public static function getTypes()
    {
        return [
            self::TYPE_WEIGHT => 'Weight',
            self::TYPE_VOLUME => 'Volume',
            self::TYPE_LENGTH => 'Length',
            self::TYPE_COUNT => 'Count',
            self::TYPE_AREA => 'Area',
            self::TYPE_TIME => 'Time',
            self::TYPE_TEMPERATURE => 'Temperature',
            self::TYPE_CURRENCY => 'Currency',
            self::TYPE_CUSTOM => 'Custom',
            self::TYPE_OTHER => 'Other',

        ];
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'measurement_unit_id', 'id');
    }

    public function baseUnit()
    {
        return $this->belongsTo(MeasurementUnit::class, 'base_unit_id');
    }

    public function derivedUnits()
    {
        return $this->hasMany(MeasurementUnit::class, 'base_unit_id');
    }

    // Helper method to get common measurement units by type


    // Helper method to get common measurement units by type
    public static function getUnitsByType($type = null)
    {
        $query = self::where('is_active', true);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }
    // Get formatted name with symbol
    public function getFormattedNameAttribute()
    {
        return "{$this->name} ({$this->symbol})";
    }
}
