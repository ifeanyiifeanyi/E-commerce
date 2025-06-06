<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\AdvertisementPackage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewAdvertisementPackageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $package;

    /**
     * Create a new notification instance.
     */
    public function __construct(AdvertisementPackage $package)
    {
        $this->package = $package;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Advertisement Package Available!')
            ->greeting("Hello {$notifiable->name},")
            ->line("We're excited to announce a new advertisement package: **{$this->package->name}**")
            ->line("Location: {$this->package->location_display}")
            ->line("Available Slots: {$this->package->max_slots}")
            ->when($this->package->description, fn($message) => $message->line("Description: {$this->package->description}"))
            ->when($this->package->features, function ($message) {
                $message->line('Features:');
                foreach ($this->package->features as $feature) {
                    $message->line("- {$feature}");
                }
            })
            ->action('View Package', url('/'))
            ->line('Thank you for choosing our platform for your advertising needs!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'package_id' => $this->package->id,
            'package_name' => $this->package->name,
            'location' => $this->package->location_display,
        ];
    }
}