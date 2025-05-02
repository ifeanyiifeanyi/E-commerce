<?php

namespace App\Channels;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Illuminate\Notifications\Notification;
use App\Notifications\PhoneVerificationNotification;

class TwilioChannel
{
    protected Client $client;

    public function __construct()
    {
        $accountSid = config('services.twilio.account_sid');
        $authToken = config('services.twilio.auth_token');

        $this->client = new Client($accountSid, $authToken);
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  PhoneVerificationNotification|Notification  $notification Notification with toTwilio method
     * @return void
     * @throws \Exception If notification doesn't have toTwilio method
     */
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toTwilio')) {
            throw new \Exception('Notification does not have toTwilio method');
        }

        // @var array{to: string, message: string} $message
        $message = $notification->toTwilio($notifiable);

        try {
            // Send the SMS via Twilio
            $result = $this->client->messages->create(
                $message['to'],
                [
                    'from' => config('services.twilio.from'),
                    'body' => $message['message']
                ]
            );

            Log::info('SMS sent via Twilio', ['sid' => $result->sid]);
        } catch (\Exception $e) {
            Log::error('Failed to send SMS via Twilio: ' . $e->getMessage());
            throw $e;
        }
    }
}
