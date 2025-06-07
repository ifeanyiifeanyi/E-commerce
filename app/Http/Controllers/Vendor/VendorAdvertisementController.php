<?php

namespace App\Http\Controllers\Vendor;

use Exception;
use Illuminate\Http\Request;
use App\Models\VendorAdvertisement;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\AdvertisementPackage;
use App\Models\AdvertisementPayment;
use Illuminate\Support\Facades\Auth;
use App\Services\AdvertisementService;
use App\Services\AdvertisementAnalyticsService;
use App\Services\AdminAdvertisementPackageService;
use App\Services\AdvertisementNotificationService;
use App\Http\Requests\StoreVendorAdvertisementRequest;

class VendorAdvertisementController extends Controller
{
    // use AuthorizesRequests;
    public function __construct(
        protected AdvertisementService $advertisementService,
        protected AdvertisementAnalyticsService $analyticsService,
        protected AdvertisementNotificationService $notificationService
    ) {}


    /**
     * Display vendor's advertisements
     */
    public function index(Request $request)
    {
        $vendorId = $request->user()->id;
        $advertisements = $this->advertisementService->getVendorSubscriptions($vendorId);
        $packages = AdvertisementPackage::where('is_active', true)
            ->with('activeAdvertisements')
            ->orderBy('sort_order')
            ->get();

        $stats = $this->analyticsService->getVendorStats($vendorId);
        $notifications = $this->notificationService->getUnreadNotifications($vendorId);

        $stats = $this->analyticsService->getVendorStats(Auth::id());
        $notifications = $this->notificationService->getUnreadNotifications(Auth::id());

        return view('vendor.advertisements.index', compact(
            'advertisements',
            'stats',
            'notifications',
            'packages'
        ));
    }


    public function packages()
    {
        $packages = AdvertisementPackage::where('is_active', true)
            ->withCount(['advertisements', 'activeAdvertisements'])
            ->orderBy('sort_order')
            ->get();

        return view('vendor.advertisements.packages', compact('packages'));
    }

    public function show(VendorAdvertisement $advertisement)
    {
        // $this->authorize('view', $advertisement);

        $stats = $this->analyticsService->getPackageStats($advertisement->id);
        $notifications = $this->notificationService->getUnreadNotifications(Auth::id());

        return view('vendor.advertisements.show', compact('advertisement', 'stats', 'notifications'));
    }

    public function showPackage(AdvertisementPackage $package)
    {
        // $this->authorize('view', $package);

        return response()->json($package);
    }

    public function subscribe($packageId = null)
    {
        // $this->authorize('subscribe', $package);
        $package = null;
        $products = null;
        if ($packageId && !is_numeric($packageId)) {
            return redirect()->route('vendor.advertisement')
                ->with('error', 'Invalid package ID provided.');
        }
        $packages = AdvertisementPackage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        $specifications = app(AdminAdvertisementPackageService::class)->getAvailableLocations();


        // add their product for the ads if it exists
        $products = Auth::user()->products;
        if ($products->isEmpty()) {
            $products = null; // No products available
        }

        if ($packageId) {
            $package = AdvertisementPackage::findOrFail($packageId);
        }


        return view('vendor.advertisements.subscribe', compact(
            'package',
            'packages',
            'specifications',
            'products'
        ));
    }

    /**
     * Store a new advertisement subscription
     */

    public function store(StoreVendorAdvertisementRequest $request)
    {
        // $this->authorize('create', VendorAdvertisement::class);
        try {
            $data = $request->validated();
            $data['vendor_id'] = Auth::id();
            $advertisement = $this->advertisementService->createAdvertisement($data, $request->file('image'));

            $paymentData = $this->advertisementService->initiatePayment($advertisement);

            // Remove the dd() for production - it was just for debugging
            // Check if we have authorization URL
            if (isset($paymentData['authorization_url'])) {
                return redirect()->away($paymentData['authorization_url']);
            }

            // If no authorization URL, redirect with error
            return redirect()->route('vendor.advertisement')
                ->with('error', 'Failed to initialize payment. Please try again.');
        } catch (Exception $e) {
            Log::error('Failed to create advertisement', [
                'message' => $e->getMessage(),
                'data' => $request->validated() ?? []
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to create advertisement: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle payment callback from Paystack
     */
    public function paymentCallback(Request $request)
    {
        try {
            // Get reference from request (Paystack sends this as a query parameter)
            $reference = $request->query('reference');

            if (!$reference) {
                return redirect()->route('vendor.advertisement')
                    ->with('error', 'Payment reference not found.');
            }

            $payment = $this->advertisementService->verifyPayment($reference);

            if ($payment->payment_status === AdvertisementPayment::PAYMENT_COMPLETED) {
                return redirect()->route('vendor.advertisement')
                    ->with('success', 'Payment successful. Advertisement is pending approval.');
            }

            return redirect()->route('vendor.advertisement')
                ->with('error', 'Payment failed. Please try again.');
        } catch (\Exception $e) {
            Log::error('Payment callback error', [
                'message' => $e->getMessage(),
                'reference' => $request->query('reference'),
            ]);

            return redirect()->route('vendor.advertisement')
                ->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Show cancellation form
     */
    public function showCancelForm(VendorAdvertisement $advertisement)
    {
        // Ensure vendor owns this advertisement
        if ($advertisement->vendor_id !== Auth::id()) {
            return redirect()->route('vendor.advertisement')
                ->with('error', 'Unauthorized action.');
        }

        // Check if advertisement can be cancelled
        $canCancel = $this->advertisementService->canCancelAdvertisement($advertisement);
        $refundAmount = 0;

        if ($canCancel) {
            $refundAmount = $this->advertisementService->calculateRefundAmount($advertisement);
        }

        return view('vendor.advertisements.cancel', compact(
            'advertisement',
            'canCancel',
            'refundAmount'
        ));
    }


    /**
     * Cancel advertisement by vendor (Updated)
     */
    public function cancel(VendorAdvertisement $advertisement, Request $request)
    {
        try {
            // Ensure vendor owns this advertisement
            if ($advertisement->vendor_id !== Auth::id()) {
                return redirect()->route('vendor.advertisement')
                    ->with('error', 'Unauthorized action.');
            }

            // Validate request
            $request->validate([
                'reason' => 'required|string|min:10|max:500',
            ]);

            // Check if advertisement can be cancelled (24-hour rule)
            if (!$this->advertisementService->canCancelAdvertisement($advertisement)) {
                return redirect()->route('vendor.advertisement')
                    ->with('error', 'This advertisement has been active for more than 24 hours and cannot be cancelled. Please contact admin for assistance.');
            }

            $reason = $request->input('reason');
            $this->advertisementService->cancelAdvertisement($advertisement, $reason);

            return redirect()->route('vendor.advertisement')
                ->with('success', 'Advertisement cancelled successfully. Refund will be processed within 5-7 business days.');
        } catch (Exception $e) {
            Log::error('Failed to cancel advertisement', [
                'message' => $e->getMessage(),
                'advertisement_id' => $advertisement->id,
                'vendor_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to cancel advertisement: ' . $e->getMessage());
        }
    }



    /**
     * Delete an advertisement
     */
    public function destroy($advert, Request $request)
    {

        $advertisement = VendorAdvertisement::findOrFail($advert);
        try {
            // Ensure vendor owns this advertisement
            if ($advertisement->vendor_id !== Auth::id()) {
                return redirect()->route('vendor.advertisement')
                    ->with('error', 'Unauthorized action.');
            }

            // Check if advertisement can be deleted
            if (!$advertisement->canBeDeleted()) {
                return redirect()->route('vendor.advertisement')
                    ->with('error', 'This advertisement cannot be deleted. Only paused, rejected, or expired ads can be removed.');
            }

            // Delete associated payments
            $advertisement->deleteAssociatedPayments();

            // Delete the advertisement
            $advertisement->delete();

            return redirect()->route('vendor.advertisement')
                ->with('success', 'Advertisement deleted successfully.');
        } catch (Exception $e) {
            Log::error('Failed to delete advertisement', [
                'message' => $e->getMessage(),
                'advertisement_id' => $advertisement->id,
                'vendor_id' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete advertisement: ' . $e->getMessage());
        }
    }
}
