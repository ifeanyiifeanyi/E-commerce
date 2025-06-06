<?php

namespace App\Services;

use App\Models\VendorAdvertisement;
use App\Models\AdvertisementNotification;

class AdvertisementNotificationService
{
     /**
     * Send expiry warning notifications
     */
    public function sendExpiryWarnings(): void
    {
        $advertisements = VendorAdvertisement::where('status', 'active')
            ->where('expires_at', '<=', now()->addDays(7))
            ->where('expires_at', '>', now())
            ->get();

        foreach ($advertisements as $advertisement) {
            // Check if warning already sent for this period
            $existingNotification = AdvertisementNotification::where('advertisement_id', $advertisement->id)
                ->where('type', 'expiry_warning')
                ->where('sent_at', '>=', now()->subDays(1))
                ->exists();

            if (!$existingNotification) {
                // SendAdvertisementNotification::dispatch($advertisement, 'expiry_warning');
            }
        }
    }

      /**
     * Send expired notifications
     */
    public function sendExpiredNotifications(): void
    {
        $advertisements = VendorAdvertisement::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($advertisements as $advertisement) {
            // Mark as expired
            $advertisement->update(['status' => 'expired']);

            // Send notification
            // SendAdvertisementNotification::dispatch($advertisement, 'expired');
        }
    }

    /**
     * Create notification record
     */
    public function createNotification(VendorAdvertisement $advertisement, string $type, string $message): AdvertisementNotification
    {
        return AdvertisementNotification::create([
            'advertisement_id' => $advertisement->id,
            'vendor_id' => $advertisement->vendor_id,
            'type' => $type,
            'message' => $message,
            'sent_at' => now(),
        ]);
    }

    /**
     * Get unread notifications for vendor
     */
    public function getUnreadNotifications(int $vendorId): \Illuminate\Support\Collection
    {
        return AdvertisementNotification::where('vendor_id', $vendorId)
            ->where('is_read', false)
            ->orderBy('sent_at', 'desc')
            ->get();
    }
    /**
     * Mark notification as read
     */
    public function markAsRead(AdvertisementNotification $notification)
    {
        return $notification->markAsRead();
    }
}
