<?php

namespace App;

interface TwilioNotification
{
    /**
     * Get the Twilio representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array<string, string>
     */
    public function toTwilio(object $notifiable): array;
}
