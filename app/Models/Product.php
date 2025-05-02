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

        'low_stock_threshold',
        'enable_stock_alerts',
        'stock_status',
        'allow_backorders',
        'track_inventory',
        'reserved_qty',
        'stock_last_updated',
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

        'enable_stock_alerts' => 'boolean',
        'allow_backorders' => 'boolean',
        'track_inventory' => 'boolean',
        'stock_last_updated' => 'datetime',
    ];

    // New relationships
    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function inventoryAlerts()
    {
        return $this->hasMany(InventoryAlert::class);
    }

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
    // New methods for inventory management
    public function getAvailableQtyAttribute()
    {
        return $this->product_qty - $this->reserved_qty;
    }

    public function isLowStock()
    {
        if (!$this->low_stock_threshold) {
            return false;
        }

        return $this->available_qty <= $this->low_stock_threshold;
    }

    public function isOutOfStock()
    {
        return $this->available_qty <= 0;
    }

    public function canBackorder()
    {
        return $this->allow_backorders && $this->stock_status !== 'discontinued';
    }

    public function updateStockStatus()
    {
        $oldStatus = $this->stock_status;

        if ($this->available_qty <= 0) {
            if ($this->allow_backorders) {
                $this->stock_status = 'backordered';
            } else {
                $this->stock_status = 'out_of_stock';
            }
        } else {
            $this->stock_status = 'in_stock';
        }

        $this->stock_last_updated = now();

        if ($oldStatus !== $this->stock_status) {
            $this->save();

            // Generate alert for out of stock
            if ($this->stock_status === 'out_of_stock') {
                $this->createInventoryAlert('out_of_stock');
            }

            // Generate alert for restock
            if ($oldStatus === 'out_of_stock' && $this->stock_status === 'in_stock') {
                $this->createInventoryAlert('restock');
            }
        }

        // Check low stock threshold
        if ($this->enable_stock_alerts && $this->isLowStock()) {
            $this->createInventoryAlert('low_stock');
        }

        return $this;
    }

    public function adjustInventory($quantity, $actionType, $userId = null, $referenceType = null, $referenceId = null, $notes = null)
    {
        if (!$this->track_inventory) {
            return $this;
        }

        $previousQty = $this->product_qty;
        $this->product_qty += $quantity;

        // Create inventory log
        $this->inventoryLogs()->create([
            'user_id' => $userId,
            'action_type' => $actionType,
            'quantity_change' => $quantity,
            'previous_quantity' => $previousQty,
            'new_quantity' => $this->product_qty,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes
        ]);

        $this->stock_last_updated = now();
        $this->save();

        // Update stock status based on new quantity
        $this->updateStockStatus();

        return $this;
    }

    public function reserveInventory($quantity, $orderId = null)
    {
        if (!$this->track_inventory) {
            return true;
        }

        // Check if we can reserve the requested quantity
        if (!$this->allow_backorders && $quantity > $this->available_qty) {
            return false;
        }

        $this->reserved_qty += $quantity;
        $this->stock_last_updated = now();
        $this->save();

        // Log the reservation
        $this->inventoryLogs()->create([
            'action_type' => 'reserve',
            'quantity_change' => 0, // No change to product_qty
            'previous_quantity' => $this->product_qty,
            'new_quantity' => $this->product_qty,
            'reference_type' => 'order',
            'reference_id' => $orderId,
            'notes' => "Reserved {$quantity} units"
        ]);

        $this->updateStockStatus();

        return true;
    }

    public function releaseReservedInventory($quantity, $orderId = null)
    {
        if (!$this->track_inventory) {
            return $this;
        }

        $this->reserved_qty = max(0, $this->reserved_qty - $quantity);
        $this->stock_last_updated = now();
        $this->save();

        // Log the release
        $this->inventoryLogs()->create([
            'action_type' => 'reserve',
            'quantity_change' => 0, // No change to product_qty
            'previous_quantity' => $this->product_qty,
            'new_quantity' => $this->product_qty,
            'reference_type' => 'order',
            'reference_id' => $orderId,
            'notes' => "Released {$quantity} reserved units"
        ]);

        $this->updateStockStatus();

        return $this;
    }

    public function createInventoryAlert($alertType, $notes = null)
    {
        // Check if there's an unresolved alert of the same type
        $existingAlert = $this->inventoryAlerts()
            ->where('alert_type', $alertType)
            ->where('is_resolved', false)
            ->first();

        if ($existingAlert) {
            // Don't create duplicate alerts
            return $existingAlert;
        }

        return $this->inventoryAlerts()->create([
            'alert_type' => $alertType,
            'notes' => $notes
        ]);
    }
}
