<?php

namespace App\Http\Controllers\Admin;

use App\Models\VendorStore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\StoreManagerService;
use Spatie\Activitylog\Models\Activity;
use App\Http\Requests\StoreStatusRequest;

class AdminVendorStoreDetailController extends Controller
{

    public function __construct(private StoreManagerService $storeService) {}

    /**
     * Display a listing of the stores.
     */
    public function index()
    {
        $stores = VendorStore::with('vendor')->simplePaginate(50);
        return view('admin.vendors.store.index', compact('stores'));
    }

    /**
     * Display the specified store details.
     */
    public function show(VendorStore $store)
    {
        // Load the vendor with their documents
        $store->load('vendor.documents');

        // Get store activity logs
        $activityLogs = Activity::causedBy(Auth::user())
            ->where('subject_type', VendorStore::class)
            ->where('subject_id', $store->id)
            ->latest()
            ->take(10)
            ->get();

        return view('admin.vendors.store.show', compact('store', 'activityLogs'));
    }

    /**
     * Approve a vendor store.
     */
    public function approve(StoreStatusRequest $request, VendorStore $store)
    {
        $this->storeService->approveStore($store);

        return redirect()->route('admin.vendor.stores.show', $store)
            ->with('success', 'Store has been approved successfully.');
    }

    /**
     * Reject a vendor store.
     */
    public function reject(StoreStatusRequest $request, VendorStore $store)
    {
        $this->storeService->rejectStore($store, $request->rejection_reason);

        return redirect()->route('admin.vendor.stores.show', $store)
            ->with('success', 'Store has been rejected with reason provided.');
    }

    /**
     * Delete a vendor store.
     */
    public function destroy(VendorStore $store)
    {
        $this->storeService->deleteStore($store);

        return redirect()->route('admin.vendor.stores')
            ->with('success', 'Store has been deleted successfully.');
    }
    /**
     * Show vendor documents associated with a store.
     */
    public function documents(VendorStore $store)
    {
        $user = $store->vendor;
        $user->load('documents');

        return view('admin.vendors.store.documents', compact('store', 'user'));
    }

    /**
     * Toggle featured status of a store.
     */
    public function toggleFeatured(VendorStore $store)
    {
        $this->storeService->toggleFeaturedStatus($store);

        return redirect()->back()
            ->with('success', 'Store featured status has been updated.');
    }
}
