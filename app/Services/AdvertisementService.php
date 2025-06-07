<?php

namespace App\Services;

use Exception;
use Yabacon\Paystack;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Models\VendorAdvertisement;
use Illuminate\Support\Facades\Log;
use App\Models\AdvertisementPackage;
use App\Models\AdvertisementPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;

class AdvertisementService
{
    protected $paystack;

    public function __construct()
    {
        $this->paystack = new Paystack(config('services.paystack.secret_key') ?? env('PAYSTACK_SECRET_KEY'));
    }

    /**
     * Get vendor's subscriptions
     */
    public function getVendorSubscriptions(int $vendorId): \Illuminate\Database\Eloquent\Collection
    {
        return VendorAdvertisement::with(['package', 'payments'])
            ->where('vendor_id', $vendorId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create a new advertisement
     */
    public function createAdvertisement(array $data, UploadedFile $image)
    {
        DB::beginTransaction();

        try {
            $package = AdvertisementPackage::findOrFail($data['package_id']);

            // Check if package is available
            if (!$package->isAvailable()) {
                throw new \Exception('Advertisement package is not available or fully booked.');
            }

            // Handle image upload
            if ($image) {
                $location = app(AdminAdvertisementPackageService::class)
                    ->getAvailableLocations()[$package->location] ?? null;
                $directory = 'advertisements';
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $directory . '/' . $fileName;

                // Ensure the advertisements directory exists
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }

                // Create new manager instance with Imagick driver
                $manager = new ImageManager(new Driver());
                $image = $manager->read($image->getRealPath())->scale(
                    width: $location['dimensions']['width'],
                    height: $location['dimensions']['height']
                );
                $image->save(public_path('storage/' . $path));
                $data['image_path'] = $path;
            }

            // Calculate dates
            $startDate = now();
            $endDate = $startDate->copy()->addDays($package->duration_days);

            $advertisement = VendorAdvertisement::create([
                'vendor_id' => $data['vendor_id'],
                'product_id' => $data['product_id'] ?? null,
                'package_id' => $data['package_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'image_path' => $data['image_path'] ?? null,
                'link_url' => $data['link_url'] ?? null,
                'amount_paid' => $package->price,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'expires_at' => $endDate,
                'status' => AdvertisementPayment::PAYMENT_PENDING,
                'payment_status' => AdvertisementPayment::PAYMENT_PENDING,
                'auto_renew' => $data['auto_renew'] ?? false,
            ]);

            DB::commit();

            // Send notification to admin for approval
            // SendAdvertisementNotification::dispatch($advertisement, 'admin_approval_needed');

            return $advertisement;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create advertisement', [
                'message' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Generate a unique transaction reference
     */
    private function generateReference(): string
    {
        return 'ADV' . time() . rand(1000, 9999);
    }

    /**
     * Process advertisement payment
     */
    public function initiatePayment(VendorAdvertisement $advertisement)
    {
        try {
            $reference = $this->generateReference();

            $data = [
                'amount' => $advertisement->amount_paid * 100, // Convert to kobo
                'email' => $advertisement->vendor->email,
                'reference' => $reference,
                'callback_url' => route('vendor.advertisements.payment.callback'),
                'metadata' => [
                    'advertisement_id' => $advertisement->id,
                    'vendor_id' => $advertisement->vendor_id,
                    'package_id' => $advertisement->package_id,
                    'product_id' => $advertisement->product_id,
                    'title' => $advertisement->title,
                    'vendor_name' => Auth::user()->name ?? $advertisement->vendor->name,
                    'advertisement_type' => $advertisement->package->type,
                ],
            ];

            // Initialize transaction using Yabacon Paystack
            $tranx = $this->paystack->transaction->initialize($data);

            if (!$tranx->status) {
                throw new Exception('Failed to initialize payment: ' . $tranx->message);
            }

            // Create payment record
            $payment = AdvertisementPayment::create([
                'advertisement_id' => $advertisement->id,
                'vendor_id' => Auth::id(),
                'payment_reference' => $reference,
                'amount' => $advertisement->amount_paid,
                'payment_method' => AdvertisementPayment::CARD_PAYMENT,
                'payment_status' => AdvertisementPayment::PAYMENT_PENDING,
                'payment_data' => json_encode($tranx->data)
            ]);

            return [
                'authorization_url' => $tranx->data->authorization_url,
                'reference' => $reference
            ];
        } catch (Exception $e) {
            Log::error("Failed to initiate payment", [
                'message' => $e->getMessage(),
                'advertisement_id' => $advertisement->id,
            ]);
            throw new Exception('Failed to initiate payment: ' . $e->getMessage());
        }
    }

    public function verifyPayment(string $reference): AdvertisementPayment
    {
        try {
            $payment = AdvertisementPayment::where('payment_reference', $reference)->first();

            if (!$payment) {
                throw new Exception('Payment not found');
            }

            // Verify payment with Yabacon Paystack
            $tranx = $this->paystack->transaction->verify([
                'reference' => $reference
            ]);

            if (!$tranx->status) {
                throw new Exception('Payment verification failed: ' . $tranx->message);
            }

            // Check if payment was successful
            if ($tranx->data->status === 'success') {
                // Update payment status
                $payment->update([
                    'payment_status' => AdvertisementPayment::PAYMENT_COMPLETED,
                    'payment_date' => now(),
                    'payment_data' => json_encode($tranx->data)
                ]);

                // Update advertisement status
                $payment->advertisement->update([
                    'status' => VendorAdvertisement::STATUS_PENDING,
                    'payment_status' => AdvertisementPayment::PAYMENT_COMPLETED
                ]);
            } else {
                // Payment failed
                $payment->update([
                    'payment_status' => AdvertisementPayment::PAYMENT_FAILED,
                    'payment_data' => json_encode($tranx->data) ?? null
                ]);
            }

            return $payment;
        } catch (Exception $e) {
            Log::error("Failed to verify payment", [
                'message' => $e->getMessage(),
                'reference' => $reference,
            ]);
            throw new Exception('Failed to verify payment: ' . $e->getMessage());
        }
    }

    /**
     * Check if advertisement can be cancelled (24-hour rule)
     */
    public function canCancelAdvertisement(VendorAdvertisement $advertisement): bool
    {
        // Check if advertisement is in cancellable status
        if (!in_array($advertisement->status, ['pending', 'active'])) {
            return false;
        }

        // Check 24-hour rule for active advertisements
        if ($advertisement->status === 'active') {
            $hoursActive = $advertisement->start_date->diffInHours(now());
            if ($hoursActive >= 24) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate refund amount for cancellation
     */
    public function calculateRefundAmount(VendorAdvertisement $advertisement)
    {
        $refundAmount = 0;
        if ($advertisement->status === VendorAdvertisement::STATUS_ACTIVE) {
            $hoursActive = $advertisement->start_date->diffInHours(now());

            if ($hoursActive >= 24) {
                return 0; // No refund if more than 24 hours have passed
            }

            $totalDays = $advertisement->start_date->diffInDays($advertisement->end_date);
            $remainingDays = now()->diffInDays($advertisement->end_date, false);
            if ($remainingDays > 0) {
                $refundAmount = ($advertisement->amount_paid * $remainingDays) / $totalDays;
            }
        } else if ($advertisement->status === VendorAdvertisement::STATUS_PENDING) {
            $refundAmount = $advertisement->amount_paid;
        }

        return round($refundAmount, 2);
    }

    /**
     * Process Paystack refund with proper error handling and timeouts
     */
    private function processPaystackRefund(string $transactionReference, float $refundAmount): array
    {
        try {
            // Set longer timeout for refund requests
            $originalTimeout = ini_get('default_socket_timeout');
            ini_set('default_socket_timeout', 60); // 60 seconds timeout

            $refundData = [
                'transaction' => $transactionReference,
                'amount' => (int) round($refundAmount * 100), // Convert to kobo and ensure integer
            ];

            Log::info('Initiating Paystack refund', [
                'transaction_reference' => $transactionReference,
                'refund_amount' => $refundAmount,
                'refund_data' => $refundData
            ]);

            // Make direct HTTP request to Paystack refund API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . (config('services.paystack.secret_key') ?? env('PAYSTACK_SECRET_KEY')),
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/refund', $refundData);

            // Restore original timeout
            ini_set('default_socket_timeout', $originalTimeout);

            $refundResult = $response->json();

            if ($response->successful() && isset($refundResult['status']) && $refundResult['status'] === true) {
                Log::info('Paystack refund successful', [
                    'transaction_reference' => $transactionReference,
                    'refund_data' => $refundResult['data']
                ]);

                return [
                    'success' => true,
                    'data' => $refundResult['data'],
                    'message' => 'Refund processed successfully'
                ];
            } else {
                Log::warning('Paystack refund failed', [
                    'transaction_reference' => $transactionReference,
                    'message' => $refundResult['message'] ?? 'Unknown error',
                    'response' => $refundResult
                ]);

                return [
                    'success' => false,
                    'message' => $refundResult['message'] ?? 'Refund failed'
                ];
            }
        } catch (\Exception $e) {
            // Restore original timeout in case of exception
            if (isset($originalTimeout)) {
                ini_set('default_socket_timeout', $originalTimeout);
            }

            Log::error('Paystack refund exception', [
                'transaction_reference' => $transactionReference,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode()
            ]);

            // Check for specific timeout or network errors
            if (
                strpos($e->getMessage(), 'timeout') !== false ||
                strpos($e->getMessage(), 'cURL error 28') !== false
            ) {
                return [
                    'success' => false,
                    'message' => 'Network timeout occurred. Refund may still be processed. Please contact support if needed.',
                    'timeout' => true
                ];
            }

            return [
                'success' => false,
                'message' => 'Refund processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cancel advertisement by vendor
     */
    public function cancelAdvertisement(VendorAdvertisement $advertisement, string $reason = ''): bool
    {
        DB::beginTransaction();

        try {
            // Check if advertisement can be cancelled (24-hour rule)
            if (!$this->canCancelAdvertisement($advertisement)) {
                throw new Exception('Advertisement cannot be cancelled. It has been active for more than 24 hours or is in an invalid status.');
            }

            // Calculate refund amount
            $refundAmount = $this->calculateRefundAmount($advertisement);

            if ($refundAmount > 0) {
                // Process refund
                $payment = $advertisement->payments()
                    ->where('payment_status', AdvertisementPayment::PAYMENT_COMPLETED)
                    ->first();

                if ($payment) {
                    $refundResult = $this->processPaystackRefund($payment->payment_reference, $refundAmount);

                    if ($refundResult['success']) {
                        // Create refund payment record
                        AdvertisementPayment::create([
                            'advertisement_id' => $advertisement->id,
                            'vendor_id' => $advertisement->vendor_id,
                            'payment_reference' => 'CANCEL_REFUND_' . $payment->payment_reference,
                            'amount' => -$refundAmount,
                            'payment_method' => AdvertisementPayment::WALLET_PAYMENT,
                            'payment_status' => AdvertisementPayment::PAYMENT_REFUNDED,
                            'refund_reason' => $reason,
                            'refunded_at' => now(),
                            'payment_date' => now(),
                            'notes' => 'Cancellation refund: ' . $reason,
                            'payment_data' => json_encode($refundResult['data'])
                        ]);
                    } else {
                        Log::warning('Refund initiation failed', [
                            'message' => $refundResult['message'] ?? 'Unknown error',
                            'payment_reference' => $payment->payment_reference,
                            'refund_amount' => $refundAmount
                        ]);
                        // Continue with cancellation even if refund fails
                    }
                    // Update advertisement status
                    $advertisement->update([
                        'status' => VendorAdvertisement::STATUS_PAUSED,
                        'cancellation_reason' => $reason,
                        'cancelled_at' => now(),
                        'cancelled_by' => Auth::id(),
                        'payment_status' => VendorAdvertisement::PAYMENT_STATUS_REFUNDED
                    ]);
                }
            }



            DB::commit();

            // Send cancellation notification
            // SendAdvertisementNotification::dispatch($advertisement, 'cancelled', $reason);

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel advertisement', [
                'message' => $e->getMessage(),
                'advertisement_id' => $advertisement->id,
            ]);
            throw new Exception('Failed to cancel advertisement: ' . $e->getMessage());
        }
    }












    

    public function renewAdvertisement(VendorAdvertisement $advertisement)
    {
        DB::beginTransaction();
        try {
            $package = $advertisement->package;
            $newEndDate = now()->addDays($package->duration_days);
            if (!$package->isAvailable()) {
                throw new Exception('Advertisement package is not available for renewal.');
            }

            // Update advertisement dates
            $advertisement->update([
                'start_date' => now(),
                'expires_at' => $newEndDate,
                'end_date' => $newEndDate,
                'status' => AdvertisementPayment::PAYMENT_PENDING,
                'payment_status' => AdvertisementPayment::PAYMENT_PENDING,
            ]);
            $paymentData = $this->initiatePayment($advertisement);
            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to renew advertisement', [
                'message' => $e->getMessage(),
                'advertisement_id' => $advertisement->id,
            ]);
            throw new Exception('Failed to renew advertisement');
        }
    }

    /**
     * Approve advertisement
     */
    public function approveAdvertisement(VendorAdvertisement $advertisement, string $adminNotes = ''): bool
    {
        $advertisement->update([
            'status' => 'active',
            'admin_notes' => $adminNotes,
        ]);

        // Send approval notification to vendor
        // SendAdvertisementNotification::dispatch($advertisement, 'approved');

        return true;
    }

    /**
     * Reject advertisement
     */
    public function rejectAdvertisement(VendorAdvertisement $advertisement, string $reason): bool
    {
        $advertisement->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        // Send rejection notification to vendor
        // SendAdvertisementNotification::dispatch($advertisement, 'rejected', $reason);

        return true;
    }

    /**
     * Get active advertisements for a specific location
     */
    public function getActiveAdvertisements(string $location, int $limit = 0): \Illuminate\Support\Collection
    {
        $query = VendorAdvertisement::with(['vendor', 'product', 'package'])
            ->whereHas('package', function ($q) use ($location) {
                $q->where('location', $location);
            })
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Record advertisement impression
     */
    public function recordImpression(VendorAdvertisement $advertisement): void
    {
        $advertisement->recordImpression();
    }

    /**
     * Record advertisement click
     */
    public function recordClick(VendorAdvertisement $advertisement): void
    {
        $advertisement->recordClick();
    }

    /**
     * Extend advertisement duration
     */
    public function extendAdvertisement(VendorAdvertisement $advertisement, int $additionalDays, float $amount): bool
    {
        DB::beginTransaction();

        try {
            $newEndDate = $advertisement->end_date->addDays($additionalDays);

            $advertisement->update([
                'end_date' => $newEndDate,
                'expires_at' => $newEndDate,
            ]);

            // Create payment record for extension
            AdvertisementPayment::create([
                'advertisement_id' => $advertisement->id,
                'vendor_id' => $advertisement->vendor_id,
                'payment_reference' => 'EXT-' . uniqid(),
                'amount' => $amount,
                'payment_method' => 'extension',
                'payment_status' => 'completed',
                'payment_date' => now(),
                'notes' => "Extended for {$additionalDays} days",
            ]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get advertisement analytics
     */
    public function getAnalytics(VendorAdvertisement $advertisement, int $days = 30): array
    {
        $analytics = $advertisement->analytics()
            ->where('date', '>=', now()->subDays($days))
            ->orderBy('date')
            ->get();

        return [
            'total_impressions' => $analytics->sum('impressions'),
            'total_clicks' => $analytics->sum('clicks'),
            'average_ctr' => $analytics->avg('ctr'),
            'daily_data' => $analytics->toArray(),
        ];
    }
}
