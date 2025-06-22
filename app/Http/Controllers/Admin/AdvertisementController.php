<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\VendorAdvertisement;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\AdvertisementPackage;
use Illuminate\Support\Facades\Auth;
use App\Services\AdvertisementService;
use App\Services\AdvertisementAnalyticsService;
use App\Services\AdminAdvertisementPackageService;
use App\Services\AdvertisementNotificationService;
use App\Http\Requests\StoreAdvertisementPackageRequest;
use App\Http\Requests\UpdateAdvertisementPackageRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdvertisementController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected AdvertisementAnalyticsService $analyticsService,
        protected AdvertisementNotificationService $notificationService,
        protected AdminAdvertisementPackageService $packageService

    ) {}


    /**
     * Display advertisement packages with analytics
     */
    public function index()
    {
        $packages = AdvertisementPackage::with(['advertisements', 'activeAdvertisements'])
            ->withCount(['advertisements', 'activeAdvertisements'])
            ->orderBy('sort_order')
            ->get();

        // Get overall statistics
        $totalPackages = $packages->count();
        $activePackages = $packages->where('is_active', true)->count();
        $totalRevenue = VendorAdvertisement::sum('amount_paid');
        $activeAdvertisements = VendorAdvertisement::where('status', 'active')
            ->where('expires_at', '>', now())
            ->count();

        // Package performance stats
        $packageStats = [];
        foreach ($packages as $package) {
            $packageStats[$package->id] = $this->analyticsService->getPackageStats($package->id);
        }

        return view('admin.ads.index', compact(
            'packages',
            'packageStats',
            'totalPackages',
            'activePackages',
            'totalRevenue',
            'activeAdvertisements'
        ));
    }

    public function create()
    {

        $locations = $this->packageService->getAvailableLocations();

        return view('admin.ads.create', compact('locations'));
    }

    public function store(StoreAdvertisementPackageRequest $request)
    {
        try {
            $package = $this->packageService->store($request->validated());

            return redirect()->route('admin.advertisement.packages')
                ->with('success', 'Advertisement package created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create advertisement package', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }


    public function edit(AdvertisementPackage $package)
    {
        $this->authorize('update', $package);
        $locations = $this->packageService->getAvailableLocations();

        return view('admin.ads.edit', compact('package', 'locations'));
    }

    public function update(UpdateAdvertisementPackageRequest $request, AdvertisementPackage $package)
    {
        try {
            $this->packageService->update($package, $request->validated());
            return redirect()->route('admin.advertisement.packages')
                ->with('success', 'Advertisement package updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update advertisement package', [
                'error' => $e->getMessage()
            ]);
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(AdvertisementPackage $package)
    {
        $this->authorize('view', $package);

        $advertisements = $package->advertisements()
            ->with(['vendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $statistics = $this->analyticsService->getPackageStats($package->id);


        return view('admin.ads.show', compact('package', 'advertisements'));
    }

    public function destroy(AdvertisementPackage $package)
    {
        $this->authorize('delete', $package);

        try {
            $this->packageService->delete($package);
            return response()->json([
                'success' => true,
                'message' => 'Advertisement package deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
