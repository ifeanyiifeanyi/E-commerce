<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\AdvertisementPackage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewAdvertisementPackageNotification;

class AdminAdvertisementPackageService
{


    public function store(array $data): AdvertisementPackage
    {
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;
        $data['features'] = array_filter($data['features'] ?? []);

        $package = AdvertisementPackage::create($data);

        if (isset($data['notify_vendors']) && $data['notify_vendors']) {
            $this->notifyVendors($package);
        }
        activity()
            ->performedOn($package)
            ->log('Advertisement package created');

        return $package;
    }

    /**
     * Update an existing advertisement package
     */
    public function update(AdvertisementPackage $package, array $data): AdvertisementPackage
    {
        $originalData = $package->toArray();

        // Check if critical data is being modified
        $criticalFields = ['price', 'duration_days', 'max_slots'];
        $criticalDataChanged = false;

        foreach ($criticalFields as $field) {
            if (isset($data[$field]) && $data[$field] != $package->$field) {
                $criticalDataChanged = true;
                break;
            }
        }

        // If package has active subscribers and critical data changed, throw exception
        if ($criticalDataChanged && $package->activeAdvertisements()->exists()) {
            throw new \Exception('Cannot modify critical package data while there are active subscriptions.');
        }

        // Update slug if name changed
        if (isset($data['name']) && $data['name'] !== $package->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['features'])) {
            $data['features'] = array_filter($data['features']);
        }

        $package->update($data);
        if (isset($data['notify_vendors']) && $data['notify_vendors']) {
            $this->notifyVendors($package);
        }

        activity()
            ->performedOn($package)
            ->withProperties(['old' => $originalData, 'new' => $package->fresh()->toArray()])
            ->log('Advertisement package updated');

        return $package->fresh();
    }

    public function delete(AdvertisementPackage $package): bool
    {
        // Check if package has any advertisements
        if ($package->advertisements()->exists()) {
            throw new \Exception('Cannot delete package with existing advertisements. Please contact subscribers first.');
        }

        activity()
            ->performedOn($package)
            ->log('Advertisement package deleted');

        return $package->delete();
    }

    /**
     * Toggle package status
     */
    public function toggleStatus(AdvertisementPackage $package): AdvertisementPackage
    {
        $package->update(['is_active' => !$package->is_active]);

        activity()
            ->performedOn($package)
            ->log('Advertisement package status toggled to ' . ($package->is_active ? 'active' : 'inactive'));

        return $package;
    }

    /**
     * Notify vendors about a new or updated package
     */
    protected function notifyVendors(AdvertisementPackage $package): void
    {
        $vendors = User::where('role', 'vendor')->get();
        $vendors = $vendors->filter(fn($vendor) =>
            $vendor->status == 'active' && $vendor->account_status == 'active');
        // dd($vendors);
        if ($vendors->isEmpty()) {
            return; // No active vendors to notify
        }
        Notification::send($vendors, new NewAdvertisementPackageNotification($package));
    }

    public function getAvailableLocations(): array
    {
        return [
            'home_banner' => [
                'label' => 'Home Page Banner',
                'description' => 'Displayed prominently at the top of the home page',
                'recommended_size' => '1200x300 pixels',
                'dimensions' => ['width' => 1200, 'height' => 300],
                'max_file_size' => 2048 // KB
            ],
            'home_sidebar' => [
                'label' => 'Home Page Sidebar',
                'description' => 'Vertical banner in the sidebar of the home page',
                'recommended_size' => '300x600 pixels',
                'dimensions' => ['width' => 300, 'height' => 600],
                'max_file_size' => 1024
            ],
            'category_top' => [
                'label' => 'Category Page Top',
                'description' => 'Displayed at the top of category pages',
                'recommended_size' => '728x90 pixels',
                'dimensions' => ['width' => 728, 'height' => 90],
                'max_file_size' => 1024
            ],
            'product_detail' => [
                'label' => 'Product Detail Page',
                'description' => 'Displayed on product detail pages',
                'recommended_size' => '468x60 pixels',
                'dimensions' => ['width' => 468, 'height' => 60],
                'max_file_size' => 512
            ],
            'search_results' => [
                'label' => 'Search Results Page',
                'description' => 'Displayed on search results pages',
                'recommended_size' => '728x90 pixels',
                'dimensions' => ['width' => 728, 'height' => 90],
                'max_file_size' => 1024
            ]
        ];
    }
}
