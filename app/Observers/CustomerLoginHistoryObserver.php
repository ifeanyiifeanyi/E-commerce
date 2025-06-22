<?php

namespace App\Observers;

use App\Models\CustomerActivityLog;
use Illuminate\Support\Facades\Log;
use App\Models\CustomerLoginHistory;
use App\Models\CustomerNotification;
use App\Jobs\CreateCustomerNotification;
use Spatie\Activitylog\Models\Activity;



class CustomerLoginHistoryObserver
{
    /**
     * Handle the CustomerLoginHistory "created" event.
     */
    public function created(CustomerLoginHistory $loginHistory): void
    {
        try {
            // 1. Log activity using Spatie Activity Log
            $this->logSpatieActivity($loginHistory);

            // 2. Log in CustomerActivityLog
            $this->logCustomerActivity($loginHistory);

            // 3. Create notifications for important events
            $this->createNotifications($loginHistory);

            // 4. Dispatch job for additional processing
            CreateCustomerNotification::dispatch($loginHistory);

        } catch (\Exception $e) {
            Log::error('Failed to process login history: ' . $e->getMessage(), [
                'login_history_id' => $loginHistory->id,
                'user_id' => $loginHistory->user_id,
                'error' => $e->getMessage()
            ]);
        }
    }


/**
     * Log activity using Spatie Activity Log
     */
    protected function logSpatieActivity(CustomerLoginHistory $loginHistory): void
    {
        $user = $loginHistory->user;
        $location = $loginHistory->getLocationAttribute();

        $description = $loginHistory->successful
            ? "Successful login from {$location}"
            : "Failed login attempt from {$location}: {$loginHistory->failure_reason}";

        $activityName = $loginHistory->successful ? 'successful_login' : 'failed_login';

        activity($activityName)
            ->causedBy($user)
            ->performedOn($loginHistory)
            ->withProperties([
                'ip_address' => $loginHistory->ip_address,
                'device_info' => $loginHistory->getDeviceInfoAttribute(),
                'browser_info' => $loginHistory->getBrowserInfoAttribute(),
                'os_info' => $loginHistory->getOsInfoAttribute(),
                'location' => $location,
                'successful' => $loginHistory->successful,
                'failure_reason' => $loginHistory->failure_reason,
                'user_agent' => $loginHistory->user_agent,
                'coordinates' => [
                    'latitude' => $loginHistory->latitude,
                    'longitude' => $loginHistory->longitude,
                ],
                'timestamp' => $loginHistory->created_at,
            ])
            ->log($description);
    }

     /**
     * Log in CustomerActivityLog
     */
    protected function logCustomerActivity(CustomerLoginHistory $loginHistory): void
    {
        if (!$loginHistory->user_id) {
            return; // Skip if no user (failed login with invalid email)
        }

        $activityType = $loginHistory->successful ? 'security' : 'system';
        $location = $loginHistory->getLocationAttribute();

        $description = $loginHistory->successful
            ? "User successfully logged in from {$location}"
            : "Failed login attempt from {$location}";

        CustomerActivityLog::log(
            $loginHistory->user_id,
            $activityType,
            $description,
            [
                'login_history_id' => $loginHistory->id,
                'ip_address' => $loginHistory->ip_address,
                'device_type' => $loginHistory->device_type,
                'device_name' => $loginHistory->device_name,
                'browser' => $loginHistory->browser,
                'browser_version' => $loginHistory->browser_version,
                'operating_system' => $loginHistory->operating_system,
                'os_version' => $loginHistory->os_version,
                'location_city' => $loginHistory->location_city,
                'location_state' => $loginHistory->location_state,
                'location_country' => $loginHistory->location_country,
                'coordinates' => [
                    'latitude' => $loginHistory->latitude,
                    'longitude' => $loginHistory->longitude,
                ],
                'successful' => $loginHistory->successful,
                'failure_reason' => $loginHistory->failure_reason,
                'user_agent' => $loginHistory->user_agent,
            ]
        );
    }

    /**
     /**
     * Create notifications for important login events
     */
    protected function createNotifications(CustomerLoginHistory $loginHistory): void
    {
        if (!$loginHistory->user_id) {
            return; // Skip if no user
        }

        // Check for suspicious activity
        $this->checkSuspiciousActivity($loginHistory);

        // Check for multiple failed attempts
        if (!$loginHistory->successful) {
            $this->checkMultipleFailedAttempts($loginHistory);
        }

        // Check for new device/location
        if ($loginHistory->successful) {
            $this->checkNewDeviceOrLocation($loginHistory);
        }
    }
    /**
     * Check for suspicious login activity
     */
    protected function checkSuspiciousActivity(CustomerLoginHistory $loginHistory): void
    {
        $user = $loginHistory->user;
        $recentLogins = CustomerLoginHistory::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours(1))
            ->where('id', '!=', $loginHistory->id)
            ->get();

        // Check for logins from different countries within short time
        $countries = $recentLogins->pluck('location_country')->unique()->filter();
        if ($countries->count() > 1 && $loginHistory->location_country) {
            CustomerNotification::create([
                'user_id' => $user->id,
                'title' => 'Suspicious Activity Detected',
                'message' => 'Login attempts detected from multiple countries within a short time period. Please verify your account security.',
                'notification_type' => 'security',
                'link_url' => route('user.security'),
            ]);
        }
    }

    /**
     * Check for multiple failed login attempts
     */
    protected function checkMultipleFailedAttempts(CustomerLoginHistory $loginHistory): void
    {
        $user = $loginHistory->user;
        $recentFailedAttempts = CustomerLoginHistory::where('user_id', $user->id)
            ->where('successful', false)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->count();

        if ($recentFailedAttempts >= 3) {
            CustomerNotification::create([
                'user_id' => $user->id,
                'title' => 'Multiple Failed Login Attempts',
                'message' => "There have been {$recentFailedAttempts} failed login attempts on your account in the last 15 minutes from {$loginHistory->getLocationAttribute()}.",
                'notification_type' => 'security',
                'link_url' => route('user.security'),
            ]);
        }
    }

     /**
     * Check for new device or location
     */
    protected function checkNewDeviceOrLocation(CustomerLoginHistory $loginHistory): void
    {
        $user = $loginHistory->user;

        // Check for new device
        $existingDevice = CustomerLoginHistory::where('user_id', $user->id)
            ->where('successful', true)
            ->where('device_type', $loginHistory->device_type)
            ->where('browser', $loginHistory->browser)
            ->where('id', '!=', $loginHistory->id)
            ->exists();

        if (!$existingDevice) {
            CustomerNotification::create([
                'user_id' => $user->id,
                'title' => 'New Device Login',
                'message' => "A login was detected from a new {$loginHistory->device_type} device using {$loginHistory->browser} from {$loginHistory->getLocationAttribute()}.",
                'notification_type' => 'security',
                'link_url' => route('user.security'),
            ]);
        }

        // Check for new location
        if ($loginHistory->location_city) {
            $existingLocation = CustomerLoginHistory::where('user_id', $user->id)
                ->where('successful', true)
                ->where('location_city', $loginHistory->location_city)
                ->where('id', '!=', $loginHistory->id)
                ->exists();

            if (!$existingLocation) {
                CustomerNotification::create([
                    'user_id' => $user->id,
                    'title' => 'New Location Login',
                    'message' => "A successful login was detected from a new location: {$loginHistory->getLocationAttribute()}.",
                    'notification_type' => 'security',
                    'link_url' => route('user.security'),
                ]);
            }
        }
    }

}
