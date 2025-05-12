<?php

namespace App\Services;

use App\Models\User;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\CustomerEmailCampaign;
use App\Mail\VerificationReminderEmail;
use App\Mail\CustomerRecommendationEmail;

class CustomerEmailService
{
    /**
     * Send product recommendations to customer
     */
    public function sendProductRecommendations(User $customer, array $products, string $subject = null)
    {
        // Skip if customer has unsubscribed from marketing emails
        if (!$this->canReceiveMarketingEmails($customer)) {
            return false;
        }

        $subject = $subject ?? 'Products You Might Like';

        // Create email campaign record
        $campaign = CustomerEmailCampaign::create([
            'user_id' => $customer->id,
            'email_type' => 'product_recommendation',
            'subject' => $subject,
            'content' => json_encode($products),
        ]);

        // Send the email
        Mail::to($customer)->send(new CustomerRecommendationEmail($customer, $products, $campaign->id));

        // Update sent timestamp
        $campaign->update(['sent_at' => now()]);

        return true;
    }

    /**
     * Send welcome email to new customer
     */
    public function sendWelcomeEmail(User $customer)
    {
        // Create email campaign record
        $campaign = CustomerEmailCampaign::create([
            'user_id' => $customer->id,
            'email_type' => 'welcome',
            'subject' => 'Welcome to Our Store!',
            'content' => 'Welcome email content',
        ]);

        // Send the email
        Mail::to($customer)->send(new WelcomeEmail($customer, $campaign->id));

        // Update sent timestamp
        $campaign->update(['sent_at' => now()]);

        return true;
    }

    /**
     * Send account verification reminder
     */
    public function sendVerificationReminder(User $customer)
    {
        if ($customer->email_verified_at) {
            return false;
        }

        // Create email campaign record
        $campaign = CustomerEmailCampaign::create([
            'user_id' => $customer->id,
            'email_type' => 'verification_reminder',
            'subject' => 'Please Verify Your Email',
            'content' => 'Verification reminder content',
        ]);

        // Send the email
        Mail::to($customer)->send(new VerificationReminderEmail($customer, $campaign->id));

        // Update sent timestamp
        $campaign->update(['sent_at' => now()]);

        return true;
    }

    /**
     * Check if customer can receive marketing emails
     */
    private function canReceiveMarketingEmails(User $customer): bool
    {
        $preferences = $customer->marketing_preferences ?? [];

        return $preferences['receive_product_recommendations'] ?? true;
    }
}
