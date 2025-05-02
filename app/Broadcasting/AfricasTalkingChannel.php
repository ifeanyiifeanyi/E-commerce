<?php

namespace App\Channels;

use Illuminate\Support\Facades\Log;
use AfricasTalking\SDK\AfricasTalking;

use Illuminate\Support\Facades\Notification;

class AfricasTalkingChannel
{
    protected $africasTalking;

    public function __construct()
    {
        $username = config('services.africas_talking.username');
        $apiKey = config('services.africas_talking.api_key');
        
        $this->africasTalking = new AfricasTalking($username, $apiKey);
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toAfricasTalking')) {
            throw new \Exception('Notification does not have toAfricasTalking method');
        }

        $message = $notification->toAfricasTalking($notifiable);
        dd($message);
        
        // Get the sms service
        $sms = $this->africasTalking->sms();
        
        try {
            // Send the SMS
            $result = $sms->send([
                'to'      => $message['to'],
                'message' => $message['message'],
                'from'    => config('services.africas_talking.from')
            ]);
            
            Log::info('SMS sent via Africa\'s Talking', $result);
        } catch (\Exception $e) {
            Log::error('Failed to send SMS via Africa\'s Talking: ' . $e->getMessage());
            throw $e;
        }
    }
}