<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'brand_id',
        'category_id',
        'subcategory_id',
        'product_name',
        'product_slug',
        'product_code',
        'product_qty',
        'product_tags',
        'product_size',
        'product_color',
        'selling_price',
        'discount_price',
        'short_description',
        'long_description',
        'product_thumbnail',
        'vendor_id',
        'hot_deals',
        'featured',
        'special_offer',
        'special_deals',
        'status',
        // New measurement-related fields
        'measurement_unit_id',
        'base_unit',
        'conversion_factor',
        'is_weight_based',
        'allow_decimal_qty',
        'min_order_qty',
        'max_order_qty',
    ];

    protected $casts = [
        'hot_deals' => 'boolean',
        'featured' => 'boolean',
        'special_offer' => 'boolean',
        'special_deals' => 'boolean',
        'status' => 'boolean',
        'is_weight_based' => 'boolean',
        'allow_decimal_qty' => 'boolean',
        'conversion_factor' => 'float',
        'min_order_qty' => 'float',
        'max_order_qty' => 'float',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function productMultiImages()
    {
        return $this->hasMany(ProductMultiImage::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class, 'measurement_unit_id');
    }

    // Helper method to format quantity with appropriate unit
    public function formattedQuantity($qty = null)
    {
        $quantity = $qty ?? $this->product_qty;

        if ($this->measurementUnit) {
            return $quantity . ' ' . $this->measurementUnit->symbol;
        }

        return $quantity;
    }

    // Convert quantity to base unit
    public function convertToBaseUnit($qty)
    {
        if (!$this->conversion_factor) {
            return $qty;
        }

        return $qty * $this->conversion_factor;
    }

    // Convert from base unit to product's measurement unit
    public function convertFromBaseUnit($baseQty)
    {
        if (!$this->conversion_factor || $this->conversion_factor == 0) {
            return $baseQty;
        }

        return $baseQty / $this->conversion_factor;
    }

    // Check if quantity is valid based on min/max constraints
    public function isValidQuantity($qty)
    {
        // Check minimum
        if ($this->min_order_qty && $qty < $this->min_order_qty) {
            return false;
        }

        // Check maximum
        if ($this->max_order_qty && $qty > $this->max_order_qty) {
            return false;
        }

        // Check decimals
        if (!$this->allow_decimal_qty && floor($qty) != $qty) {
            return false;
        }

        return true;
    }

    // Get formatted price with appropriate unit
    public function getFormattedPriceAttribute()
    {
        $price = $this->discount_price ?? $this->selling_price;

        if ($this->is_weight_based && $this->measurementUnit) {
            return $price . ' / ' . $this->measurementUnit->symbol;
        }

        return $price;
    }

    // Calculate sale percentage if discount price is set
    public function getDiscountPercentageAttribute()
    {
        if (!$this->discount_price || !$this->selling_price) {
            return 0;
        }

        $discountAmount = $this->selling_price - $this->discount_price;
        return round(($discountAmount / $this->selling_price) * 100);
    }


    // Helper method to get formatted measure
    public function getFormattedMeasureAttribute()
    {
        if ($this->measurement_unit_id && $this->measurementUnit) {
            return $this->measurementUnit->symbol;
        }
        return '';
    }

    
}
