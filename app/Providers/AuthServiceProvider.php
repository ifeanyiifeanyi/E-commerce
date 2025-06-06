<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\VendorDocument;
use App\Policies\ProductPolicy;
use App\Models\AdvertisementPackage;
use App\Models\VendorAdvertisement;
use Illuminate\Support\Facades\Gate;
use App\Policies\VendorDocumentPolicy;
use Illuminate\Support\ServiceProvider;
use App\Policies\AdvertisementPackagePolicy;
use App\Policies\VendorAdvertisementPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

     /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        VendorDocument::class => VendorDocumentPolicy::class,
        Product::class => ProductPolicy::class,
        AdvertisementPackage::class => AdvertisementPackagePolicy::class,
        VendorAdvertisement::class => VendorAdvertisementPolicy::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(VendorDocument::class, VendorDocumentPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(AdvertisementPackage::class, AdvertisementPackagePolicy::class);
        Gate::policy(VendorAdvertisement::class, VendorAdvertisementPolicy::class);

    }
}
