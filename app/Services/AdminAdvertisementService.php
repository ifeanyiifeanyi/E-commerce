<?php

namespace App\Services;

use Exception;
use App\Mail\VendorMessageMail;
use Illuminate\Support\Facades\DB;
use App\Models\VendorAdvertisement;
use Illuminate\Support\Facades\Log;
use App\Models\AdvertisementPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Services\AdvertisementService;
use App\Services\AdvertisementAnalyticsService;
use App\Services\AdvertisementNotificationService;
use GuzzleHttp\Psr7\Request;

class AdminAdvertisementService
{
    public function __construct(
        protected AdvertisementService $advertisementService,
        protected AdvertisementAnalyticsService $analyticsService,
        protected AdvertisementNotificationService $notificationService
    ) {}

    /**
     * Approve an advertisement
     */
    public function approveAdvertisement(VendorAdvertisement $advertisement, string $adminNotes = ''): bool
    {
        try {
            DB::beginTransaction();
            $adminNotes = trim($adminNotes);
            if (empty($adminNotes)) {
                $adminNotes = 'Your advert placement has been approved by the admin.';
            }

            $this->advertisementService->approveAdvertisement($advertisement, $adminNotes);

            // $this->notificationService->createNotification(
            //     $advertisement,
            //     $advertisement->vendor->id,
            //     'approved',
            //     'Your advertisement "' . $advertisement->title . '" has been approved.' .
            //     ($adminNotes ? ' Notes: ' . $adminNotes : '')
            // );
            // Fixed: Correct parameter order for createNotification
            $this->notificationService->createNotification(
                $advertisement,
                'approved', // type parameter
                'Your advertisement "' . $advertisement->title . '" has been approved.' .
                    ($adminNotes ? ' Notes: ' . $adminNotes : '') // message parameter
            );

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to approve advertisement', [
                'message' => $e->getMessage(),
                'advertisement_id' => $advertisement->id,
            ]);
            throw new Exception('Failed to approve advertisement: ' . $e->getMessage());
        }
    }


      /**
     * Reject an advertisement and process refund
     */
    public function rejectAdvertisement(VendorAdvertisement $advertisement, string $reason): bool
    {
        try {
            DB::beginTransaction();

            // Check if advertisement has a completed payment
            $payment = $advertisement->payments()
                ->where('payment_status', AdvertisementPayment::PAYMENT_COMPLETED)
                ->first();

            if ($payment) {
                // dd($payment);
                // Process full refund for rejected advertisement
                $refundResult = $this->advertisementService->processPaystackRefund(
                    $payment->payment_reference,
                    $advertisement->amount_paid
                );

                if ($refundResult['success']) {
                    // Create refund payment record
                    AdvertisementPayment::create([
                        'advertisement_id' => $advertisement->id,
                        'vendor_id' => $advertisement->vendor_id,
                        'payment_reference' => 'REJECT_REFUND_' . $payment->payment_reference,
                        'amount' => -$advertisement->amount_paid,
                        'payment_method' => AdvertisementPayment::WALLET_PAYMENT,
                        'payment_status' => AdvertisementPayment::PAYMENT_REFUNDED,
                        'refund_reason' => $reason,
                        'refunded_at' => now(),
                        'payment_date' => now(),
                        'notes' => 'Rejection refund: ' . $reason,
                        'payment_data' => json_encode($refundResult['data'])
                    ]);
                } else {
                    Log::warning('Refund initiation failed during rejection', [
                        'message' => $refundResult['message'],
                        'payment_reference' => $payment->payment_reference,
                        'refund_amount' => $advertisement->amount_paid
                    ]);
                    // Continue with rejection even if refund fails
                }

                $advertisement->update([

                ]);
            }

            // Reject the advertisement
            $this->advertisementService->rejectAdvertisement($advertisement, $reason);

            // Send rejection notification
            $this->notificationService->createNotification(
                $advertisement,
                'rejected',
                'Your advertisement "' . $advertisement->title . '" has been rejected. Reason: ' . $reason
            );

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to reject advertisement', [
                'message' => $e->getMessage(),
                'advertisement_id' => $advertisement->id,
            ]);
            throw new Exception('Failed to reject advertisement: ' . $e->getMessage());
        }
    }

      /**
     * Suspend an advertisement
     */
    public function suspendAdvertisement(VendorAdvertisement $advertisement, string $reason): bool
    {
        try {
            DB::beginTransaction();

            $advertisement->update([
                'status' => VendorAdvertisement::STATUS_PAUSED,
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
                'cancelled_by' => Auth::id(),
            ]);

            $this->notificationService->createNotification(
                $advertisement,
                'rejected',
                'Your advertisement "' . $advertisement->title . '" has been suspended. Reason: ' . $reason
            );

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to suspend advertisement', [
                'message' => $e->getMessage(),
                'advertisement_id' => $advertisement->id,
            ]);
            throw new Exception('Failed to suspend advertisement: ' . $e->getMessage());
        }
    }


      /**
     * Reactivate a paused advertisement
     */
    public function reactivateAdvertisement(VendorAdvertisement $advertisement): bool
    {
        try {
            DB::beginTransaction();

            if ($advertisement->status !== VendorAdvertisement::STATUS_PAUSED) {
                throw new Exception('Only paused advertisements can be reactivated.');
            }

            if ($advertisement->expires_at <= now()) {
                throw new Exception('Advertisement has expired and cannot be reactivated.');
            }

            $advertisement->update([
                'status' => VendorAdvertisement::STATUS_ACTIVE,
                'reactivated_at' => now(),
                'reactivated_by' => Auth::id(),
            ]);

            $this->notificationService->createNotification(
                $advertisement,
                'approved',
                'Your advertisement "' . $advertisement->title . '" has been reactivated.'
            );

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to reactivate advertisement', [
                'message' => $e->getMessage(),
                'advertisement_id' => $advertisement->id,
            ]);
            throw new Exception('Failed to reactivate advertisement: ' . $e->getMessage());
        }
    }

    /**
     * Send a personalized message to the vendor
     */
    public function sendMessageToVendor(VendorAdvertisement $advertisement, string $message): bool
    {
        try {
            // Validate input
            if (empty(trim($message))) {
                throw new Exception('Message cannot be empty');
            }

            // Ensure the advertisement has its vendor relationship loaded
            if (!$advertisement->relationLoaded('vendor')) {
                $advertisement->load('vendor');
            }

            // Validate vendor exists
            if (!$advertisement->vendor) {
                throw new Exception('Vendor not found for this advertisement');
            }

            // Create notification
            $this->notificationService->createNotification(
                $advertisement,
                'approved',
                'Admin message regarding your advertisement "' . $advertisement->title . '": ' . $message
            );

            // Send email
            Mail::to($advertisement->vendor->email)->send(
                new VendorMessageMail($advertisement, $message)
            );

            Log::info('Message sent to vendor', [
                'advertisement_id' => $advertisement->id,
                'vendor_id' => $advertisement->vendor_id,
                'message' => $message,
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to send message to vendor', [
                'message' => $e->getMessage(),
                'advertisement_id' => $advertisement->id,
                'vendor_id' => $advertisement->vendor_id ?? null,
            ]);
            throw new Exception('Failed to send message: ' . $e->getMessage());
        }
    }
}
