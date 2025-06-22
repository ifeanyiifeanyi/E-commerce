<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Models\VendorAdvertisement;
use App\Http\Controllers\Controller;
use App\Models\AdvertisementPackage;
use App\Models\AdvertisementPayment;
use App\Services\AdminAdvertisementService;
use App\Services\AdvertisementService;
use App\Services\AdvertisementAnalyticsService;
use App\Services\AdvertisementNotificationService;

class ManageVendorAdvertisementSubscriptionController extends Controller
{
    public function __construct(
        protected AdvertisementService $advertisementService,
        protected AdvertisementAnalyticsService $analyticsService,
        protected AdvertisementNotificationService $notificationService,
        protected AdminAdvertisementService $adminAdvertisementService
    ) {}


    public function index(Request $request)
    {
        $query = VendorAdvertisement::with(['vendor', 'package', 'payments'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by package
        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by vendor name or advertisement title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                        $vendorQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $advertisements = $query->paginate(20)->withQueryString();
        $packages = AdvertisementPackage::all();

        // Get statistics for the dashboard
        $stats = $this->getAdminDashboardStats();

        return view('admin.ads_subscriptions.index', compact('stats', 'packages', 'advertisements'));
    }

    /**
     * Show advertisement details for admin review
     */
    public function show(VendorAdvertisement $advertisement)
    {
        $advertisement->load(['vendor', 'package', 'payments', 'analytics']);

        $analytics = $this->analyticsService->getPackageStats($advertisement->id);
        $notifications = $advertisement->notifications()->orderBy('sent_at', 'desc')->get();

        return view('admin.ads_subscriptions.show', compact(
            'advertisement',
            'analytics',
            'notifications'
        ));
    }

    /**
     * Show pending advertisements requiring approval
     */
    public function pendingAds(Request $request)
    {
        $query = VendorAdvertisement::with(['vendor', 'package'])
            ->where('status', VendorAdvertisement::STATUS_PENDING)
            ->where('payment_status', AdvertisementPayment::PAYMENT_COMPLETED)
            ->orderBy('created_at', 'asc');

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by package
        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by vendor name or advertisement title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                        $vendorQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $advertisements = $query->paginate(20)->withQueryString();
        $packages = AdvertisementPackage::all();

        return view('admin.ads_subscriptions.pending', compact('advertisements', 'packages'));
    }

    /**
     * Show active advertisements
     */
    public function activeAds(Request $request)
    {
        $query = VendorAdvertisement::with(['vendor', 'package', 'payments'])
            ->where('status', VendorAdvertisement::STATUS_ACTIVE)
            ->where('expires_at', '>', now())
            ->orderBy('expires_at', 'asc');

        // Apply common filters
        $this->applyFilters($query, $request);

        $advertisements = $query->paginate(20)->withQueryString();
        $packages = AdvertisementPackage::all();

        // Get active-specific stats
        $stats = [
            'total_active' => VendorAdvertisement::where('status', VendorAdvertisement::STATUS_ACTIVE)
                ->where('expires_at', '>', now())
                ->count(),
            'expiring_soon' => VendorAdvertisement::where('status', VendorAdvertisement::STATUS_ACTIVE)
                ->where('expires_at', '<=', now()->addDays(7))
                ->where('expires_at', '>', now())
                ->count(),
            'revenue_this_month' => VendorAdvertisement::where('status', VendorAdvertisement::STATUS_ACTIVE)
                ->whereMonth('created_at', now()->month)
                ->sum('amount_paid'),
            'avg_duration' => VendorAdvertisement::where('status', VendorAdvertisement::STATUS_ACTIVE)
                ->selectRaw('AVG(DATEDIFF(expires_at, start_date)) as avg_days')
                ->value('avg_days') ?? 0
        ];

        return view('admin.ads_subscriptions.active', compact('advertisements', 'packages', 'stats'));
    }


    public function suspendedAds(Request $request)
    {
        $query = VendorAdvertisement::with(['vendor', 'package', 'payments'])
            ->where('status', VendorAdvertisement::STATUS_PAUSED)
            ->orderBy('created_at', 'asc');

        // Apply common filters
        $this->applyFilters($query, $request);

        $advertisements = $query->paginate(20)->withQueryString();
        $packages = AdvertisementPackage::all();

        return view('admin.ads_subscriptions.suspended', compact('advertisements', 'packages'));
    }

    public function expiredAds(Request $request){
        $query = VendorAdvertisement::with(['vendor', 'package', 'payments'])
            ->where('status', VendorAdvertisement::STATUS_EXPIRED)
            ->orderBy('expires_at', 'desc');

        $advertisements = $query->paginate(20)->withQueryString();
        $packages = AdvertisementPackage::all();

        return view('admin.ads_subscriptions.expired', compact('advertisements', 'packages'));
    }


    /**
     * Reactivate a suspended advertisement
     */
    /**
     * Updated reactivate method with expiration check
     */
    public function reactivate(VendorAdvertisement $advertisement, Request $request)
    {

        // Validate the request
        $request->validate([
            'message_to_vendor' => 'nullable|string|max:1000'
        ]);

        try {
            // Check if advertisement has expired
            if ($advertisement->expires_at <= now()) {
                return redirect()->back()->with('error', 'This advertisement has expired and cannot be reactivated. Please contact the vendor to renew their package.');
            }

            // Check if advertisement is in the correct status
            if ($advertisement->status !== VendorAdvertisement::STATUS_PAUSED) {
                return redirect()->back()->with('error', 'Only suspended advertisements can be reactivated.');
            }

            $this->adminAdvertisementService->reactivateAdvertisement($advertisement);

            if ($request->filled('message_to_vendor')) {
                $this->adminAdvertisementService->sendMessageToVendor($advertisement, $request->input('message_to_vendor'));
            }
            return redirect()->back()->with('success', 'Advertisement reactivated successfully and vendor has been notified.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    /**
     * Delete an advertisement
     */
    public function destroy(VendorAdvertisement $advertisement)
    {
        try {
            // Check if advertisement can be deleted
            if (!$advertisement->canBeDeleted()) {
                return redirect()->back()->with('error', 'This advertisement cannot be deleted. Only expired, rejected, or paused advertisements can be deleted.');
            }

            // Delete associated payments and analytics
            $advertisement->deleteAssociatedPayments();
            $advertisement->analytics()->delete();
            $advertisement->notifications()->delete();

            // Delete the advertisement
            $advertisement->delete();

            return redirect()->back()->with('success', 'Advertisement deleted successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete advertisement: ' . $e->getMessage());
        }
    }



    /**
     * Apply common filters to query
     */
    private function applyFilters($query, Request $request)
    {
        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by package
        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by vendor name or advertisement title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                        $vendorQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }
    }


    /**
     * Approve an advertisement
     */
    public function approve(VendorAdvertisement $advertisement, Request $request)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
            'message_to_vendor' => 'nullable|string|max:1000'
        ]);

        try {
            // Check if advertisement is in pending status and payment is completed
            if (
                $advertisement->status !== VendorAdvertisement::STATUS_PENDING ||
                $advertisement->payment_status !== AdvertisementPayment::PAYMENT_COMPLETED
            ) {
                return redirect()->back()->with('error', 'Advertisement cannot be approved. Check status and payment.');
            }

            $this->adminAdvertisementService->approveAdvertisement($advertisement, $request->input('admin_notes', ''));

            if ($request->filled('message_to_vendor')) {
                $this->adminAdvertisementService->sendMessageToVendor($advertisement, $request->input('message_to_vendor'));
            }

            return redirect()->back()->with('success', 'Advertisement approved successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject an advertisement and process refund
     */
    public function reject(VendorAdvertisement $advertisement, Request $request)
    {
        // dd($advertisement->vendor);
        // dd($request->all());
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
            'message_to_vendor' => 'nullable|string|max:1000'
        ]);

        try {
            if ($advertisement->status !== VendorAdvertisement::STATUS_PENDING) {
                return redirect()->back()->with('error', 'Advertisement cannot be rejected from current status.');
            }

            // Process automatic refund if payment was completed
            // i am adding this if statement cos the vendor must pay, to process their ads request, right ?
            if ($advertisement->payment_status === AdvertisementPayment::PAYMENT_COMPLETED) {
                $this->adminAdvertisementService->rejectAdvertisement($advertisement, $request->input('rejection_reason'));
            } else {
                // coming directly from the final service class(built for admin & vendor usage)
                $this->advertisementService->rejectAdvertisement($advertisement, $request->input('rejection_reason'));
            }
            if ($request->filled('message_to_vendor')) {
                $this->adminAdvertisementService->sendMessageToVendor($advertisement, $request->input('message_to_vendor'));
            }

            return redirect()->back()->with('success', 'Advertisement rejected and refund processed successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Suspend an active advertisement
     */
    public function suspend(VendorAdvertisement $advertisement, Request $request)
    {
        // dd($advertisement);
        $request->validate([
            'suspension_reason' => 'required|string|min:10|max:500',
            'message_to_vendor' => 'nullable|string|max:1000'
        ]);

        try {
            if ($advertisement->status !== VendorAdvertisement::STATUS_ACTIVE) {
                return redirect()->back()->with('error', 'Only active advertisements can be suspended.');
            }

            $this->adminAdvertisementService->suspendAdvertisement($advertisement, $request->input('suspension_reason'));

            if ($request->filled('message_to_vendor')) {
                $this->adminAdvertisementService->sendMessageToVendor($advertisement, $request->input('message_to_vendor'));
            }

            return redirect()->back()->with('success', 'Advertisement suspended successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    /**
     * Send a message to vendor
     */
    public function sendVendorMessage($advertisementId, Request $request)
    {

        $advertisement = VendorAdvertisement::findOrFail($advertisementId);
        // dd($advertisement);

        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        try {
            $this->adminAdvertisementService->sendMessageToVendor(
                $advertisement,
                $request->input('message')
            );

            return redirect()->back()->with('success', 'Message sent to vendor successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to send message: ' . $e->getMessage());
        }
    }


    /**
     * Get admin dashboard statistics
     */
    private function getAdminDashboardStats(): array
    {
        return [
            'total_advertisements' => VendorAdvertisement::count(),
            'pending_approval' => VendorAdvertisement::where('status', VendorAdvertisement::STATUS_PENDING)
                ->where('payment_status', AdvertisementPayment::PAYMENT_COMPLETED)
                ->count(),
            'active_advertisements' => VendorAdvertisement::where('status', VendorAdvertisement::STATUS_ACTIVE)
                ->where('expires_at', '>', now())
                ->count(),
            'total_revenue' => VendorAdvertisement::whereHas('payments', function ($query) {
                $query->where('payment_status', AdvertisementPayment::PAYMENT_COMPLETED);
            })->sum('amount_paid'),
            'expired_today' => VendorAdvertisement::where('expires_at', '<=', now())
                ->where('expires_at', '>=', now()->startOfDay())
                ->count(),
            'expiring_soon' => VendorAdvertisement::where('status', VendorAdvertisement::STATUS_ACTIVE)
                ->where('expires_at', '<=', now()->addDays(7))
                ->where('expires_at', '>', now())
                ->count(),
            'suspended_count' => VendorAdvertisement::where('status', VendorAdvertisement::STATUS_PAUSED)->count(),
            'rejected_count' => VendorAdvertisement::where('status', VendorAdvertisement::STATUS_REJECTED)->count(),
        ];
    }
}
