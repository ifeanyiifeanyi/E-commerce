<?php

namespace App\Notifications;

use App\TwilioNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PhoneVerificationNotification extends Notification implements ShouldQueue, TwilioNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $verificationCode
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['twilio'];
    }

    /**
     * Get the Twilio representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array<string, string>
     */
    public function toTwilio(object $notifiable): array
    {
        return [
            'to' => $notifiable->phone,
            'message' => "Your verification code is: {$this->verificationCode}. Valid for 15 minutes."
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
