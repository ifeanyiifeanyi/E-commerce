<?php

namespace App\Observers;

use App\Models\CustomerActivityLog;
use App\Models\CustomerNotification;

class CustomerNotificationObserver
{
   /**
     * Handle the CustomerNotification "updated" event.
     */
    public function updated(CustomerNotification $notification): void
    {
        $changes = $notification->getChanges();

        // Check if notification was marked as read
        if (isset($changes['read_at']) && $changes['read_at'] !== null) {
            $this->logNotificationRead($notification);
        }
    }


    /**
     * Log notification read event
     */
    private function logNotificationRead(CustomerNotification $notification): void
    {
        // Custom activity log
        CustomerActivityLog::log(
            userId: $notification->user_id,
            activityType: 'notification_read',
            description: "Notification marked as read: {$notification->title}",
            properties: [
                'event' => 'notification_read',
                'subject_type' => CustomerNotification::class,
                'subject_id' => $notification->id,
                'notification_title' => $notification->title,
                'notification_type' => $notification->notification_type,
                'read_at' => $notification->read_at,
            ]
        );

        // Spatie activity log
        activity()
            ->causedBy($notification->user)
            ->performedOn($notification)
            ->withProperties([
                'event' => 'notification_read',
                'notification_title' => $notification->title,
                'notification_type' => $notification->notification_type,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Notification marked as read');
    }
    /**
     * Handle the CustomerNotification "deleted" event.
     */
    public function deleted(CustomerNotification $customerNotification): void
    {
        //
    }

    /**
     * Handle the CustomerNotification "restored" event.
     */
    public function restored(CustomerNotification $customerNotification): void
    {
        //
    }

    /**
     * Handle the CustomerNotification "force deleted" event.
     */
    public function forceDeleted(CustomerNotification $customerNotification): void
    {
        //
    }
}
