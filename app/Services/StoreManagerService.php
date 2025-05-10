<?php

namespace App\Services;

use App\Mail\StoreApproved;
use App\Mail\StoreRejected;
use App\Models\VendorStore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class StoreManagerService
{
    /**
     * Approve a vendor store
     */
    public function approveStore(VendorStore $store)
    {
        $store->update([
            'status' => 'approved',
            'rejection_reason' => null
        ]);

        // Log activity
        activity()
            ->performedOn($store)
            ->causedBy(Auth::user())
            ->withProperties([
                'status' => 'approved',
                'store_name' => $store->store_name
            ])
            ->log('Approved store');

        // Send notification email to vendor
        if ($store->vendor) {
            // dd($store->vendor->email);
            Mail::to($store->vendor->email)->send(new StoreApproved($store));
        }
    }


    /**
     * Reject a vendor store
     */
    public function rejectStore(VendorStore $store, string $rejectionReason)
    {
        $store->update([
            'status' => 'rejected',
            'rejection_reason' => $rejectionReason
        ]);

        // Log activity
        activity()
            ->performedOn($store)
            ->causedBy(Auth::user())
            ->withProperties([
                'status' => 'rejected',
                'store_name' => $store->store_name,
                'rejection_reason' => $rejectionReason
            ])
            ->log('Rejected store');

        // Send notification email to vendor
        if ($store->vendor) {
            Mail::to($store->vendor->email)->send(new StoreRejected($store));
        }
    }

    /**
     * Delete a vendor store
     */
    public function deleteStore(VendorStore $store)
    {
        // Keep track of store information for logging
        $storeInfo = [
            'id' => $store->id,
            'store_name' => $store->store_name,
            'vendor_id' => $store->user_id
        ];

        // Delete store logo and banner if they exist
        if ($store->store_logo) {
            Storage::disk('public')->delete($store->store_logo);
        }

        if ($store->store_banner) {
            Storage::disk('public')->delete($store->store_banner);
        }

        // Delete the store
        $store->delete();

        // Log activity
        activity()
            ->causedBy(Auth::user())
            ->withProperties($storeInfo)
            ->log('Deleted store');
    }

    /**
     * Toggle featured status of store
     */
    public function toggleFeaturedStatus(VendorStore $store)
    {
        $store->update([
            'is_featured' => !$store->is_featured
        ]);

        $status = $store->is_featured ? 'featured' : 'unfeatured';

        // Log activity
        activity()
            ->performedOn($store)
            ->causedBy(Auth::user())
            ->withProperties([
                'featured_status' => $status,
                'store_name' => $store->store_name
            ])
            ->log("Store {$status}");
    }
}
