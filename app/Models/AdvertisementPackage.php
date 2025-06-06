<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdvertisementPackage extends Model
{
     use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'location',
        'price',
        'duration_days',
        'max_slots',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'location', 'price', 'duration_days', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function hasAnySubscriptions()
    {
        return $this->advertisements()->exists();
    }

    public function hasActiveSubscriptions()
    {
        return $this->activeAdvertisements()->exists();

    }
     public function advertisements()
    {
        return $this->hasMany(VendorAdvertisement::class, 'package_id');
    }

    public function activeAdvertisements()
    {
        return $this->hasMany(VendorAdvertisement::class, 'package_id')
                    ->where('status', 'active')
                    ->where('expires_at', '>', now());
    }

    public function getAvailableSlotsAttribute()
    {
        return $this->max_slots - $this->activeAdvertisements()->count();
    }

    public function isAvailable()
    {
        return $this->is_active && $this->available_slots > 0;
    }

    public function getLocationDisplayAttribute()
    {
        return match($this->location) {
            'home_banner' => 'Home Page Banner',
            'home_sidebar' => 'Home Page Sidebar',
            'category_top' => 'Category Page Top',
            'product_detail' => 'Product Detail Page',
            'search_results' => 'Search Results Page',
            default => ucfirst(str_replace('_', ' ', $this->location))
        };
    }

    public function specification(){
        
    }
}
