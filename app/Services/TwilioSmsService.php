<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioSmsService
{
    protected Client $client;

    /**
     * Create a new Twilio SMS service instance.
     */
    public function __construct()
    {
        $accountSid = config('services.twilio.account_sid');
        $authToken = config('services.twilio.auth_token');

        $this->client = new Client($accountSid, $authToken);
    }

    /**
     * Send an SMS message.
     *
     * @param string $to The recipient phone number
     * @param string $message The message content
     * @param string|null $from The sender phone number (optional, uses default from config)
     * @return string The message SID if successful
     * @throws \Exception If sending fails
     */
    public function send(string $to, string $message, ?string $from = null): string
    {
        try {
            $result = $this->client->messages->create(
                $to,
                [
                    'from' => $from ?? config('services.twilio.from'),
                    'body' => $message
                ]
            );

            Log::info('SMS sent via Twilio', [
                'to' => $to,
                'sid' => $result->sid
            ]);

            return $result->sid;
        } catch (\Exception $e) {
            Log::error('Failed to send SMS via Twilio', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send a verification code SMS.
     *
     * @param string $to The recipient phone number
     * @param string $code The verification code
     * @return string The message SID
     */
    public function sendVerificationCode(string $to, string $code): string
    {
        $message = "Your verification code is: {$code}. Valid for 15 minutes.";
        return $this->send($to, $message);
    }

    /**
     * Send a welcome SMS.
     *
     * @param string $to The recipient phone number
     * @param string $name The recipient's name
     * @return string The message SID
     */
    public function sendWelcome(string $to, string $name): string
    {
        $message = "Welcome {$name}! Thank you for registering with our service.";
        return $this->send($to, $message);
    }
}
