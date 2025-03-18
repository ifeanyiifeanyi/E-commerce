<?php

namespace App\Providers;

use App\Models\VendorDocument;
use Illuminate\Support\Facades\Gate;
use App\Policies\VendorDocumentPolicy;
use Illuminate\Support\ServiceProvider;

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
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(VendorDocument::class, VendorDocumentPolicy::class);

    }
}
