<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\VendorAdvertisement;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendAdvertisementNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public String $status;
    public String $adminNotes;
    public VendorAdvertisement $advertisement;

    /**
     * Create a new notification instance.
     */
    public function __construct(VendorAdvertisement $advertisement, String $status, String $adminNotes = '')
    {
        $this->status = $status;
        $this->adminNotes = $adminNotes;
        $this->advertisement = $advertisement;
        $this->onQueue('advertisement_notifications');
        $this->delay(now()->addMinutes(5)); // Delay the notification by 5 minutes
        $this->afterCommit(); // Ensure the notification is sent after the transaction commits
        $this->advertisement->load(['vendor', 'package', 'payments']);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->status === 'approved') {
            $statusMessage = 'Your advertisement has been approved.';
        } elseif ($this->advertisement->status === 'rejected') {
            $statusMessage = 'Your advertisement has been rejected.';
        } else {
            $statusMessage = 'Your advertisement status has been updated.';
        }
        $statusMessage .= $this->adminNotes ? ' Notes: ' . $this->adminNotes : '';
        return (new MailMessage)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->subject('Advertisement Status Update: ' . $this->advertisement->title)
            ->line('Dear ' . $notifiable->name . ',')
            ->line('Your advertisement titled "' . $this->advertisement->title . '" has been ' . $this->status . '.')
            ->line($this->adminNotes ? 'Notes: ' . $this->adminNotes : '')
            ->line($statusMessage)
            ->action('View Advertisement', url('vendor/advertisements/' . $this->advertisement->id))
            ->line('Thank you for using our service!')
            ->salutation('Best regards,')
            ->line('The Advertisement Team');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->status === 'approved' ?
                'Your advertisement has been approved.' : ($this->status === 'rejected' ?
                    'Your advertisement has been rejected.' :
                    'Your advertisement status has been updated.'),
            'advertisement_id' => $this->advertisement->id,
            'advertisement_title' => $this->advertisement->title,
            'advertisement_status' => $this->advertisement->status,
            'advertisement_package' => $this->advertisement->package->name ?? 'N/A',
            'advertisement_vendor' => $this->advertisement->vendor->name ?? 'N/A',
            'advertisement_admin_notes' => $this->adminNotes,
            'advertisement_created_at' => $this->advertisement->created_at->toDateTimeString(),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->status === 'approved' ?
                'Your advertisement has been approved.' : ($this->status === 'rejected' ?
                    'Your advertisement has been rejected.' :
                    'Your advertisement status has been updated.'),
            'advertisement_id' => $this->advertisement->id,
            'advertisement_title' => $this->advertisement->title,
            'advertisement_status' => $this->advertisement->status,
            'advertisement_package' => $this->advertisement->package->name ?? 'N/A',
            'advertisement_vendor' => $this->advertisement->vendor->name ?? 'N/A',
            'advertisement_admin_notes' => $this->adminNotes,
            'advertisement_created_at' => $this->advertisement->created_at->toDateTimeString(),
        ];
    }
    public function delay(): int
    {
        return 5 * 60; // Delay in seconds (5 minutes)
    }
}
