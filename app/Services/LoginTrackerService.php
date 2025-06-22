<?php

namespace App\Services;

use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginTrackerService
{
    protected $agent;
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->agent = new Agent();
    }

     /**
     * Collect login data from request
     */
    public function collectLoginData(Request $request): array
    {
        return [
            'ip' => $this->getClientIp($request),
            'device_info' => $this->getDeviceInfo(),
            'browser_info' => $this->getBrowserInfo(),
            'os_info' => $this->getOsInfo(),
            'latitude' => $request->input('latitude'), // From frontend geolocation
            'longitude' => $request->input('longitude'), // From frontend geolocation
            'registration_source' => $request->input('registration_source', 'web'), // Default to 'web'
            'referral_source' => $this->getReferralSource($request),
        ];
    }

     /**
     * Get client IP address
     */
    protected function getClientIp(Request $request): string
    {
        return $request->ip();
    }

    /**
     * Get device information
     */
    protected function getDeviceInfo(): string
    {
        if ($this->agent->isMobile()) {
            return 'Mobile - ' . $this->agent->device();
        } elseif ($this->agent->isTablet()) {
            return 'Tablet - ' . $this->agent->device();
        } elseif ($this->agent->isDesktop()) {
            return 'Desktop';
        }

        return 'Unknown Device';
    }

      /**
     * Get browser information
     */
    protected function getBrowserInfo(): string
    {
        return $this->agent->browser() . ' ' . $this->agent->version($this->agent->browser());
    }

    /**
     * Get operating system information
     */
    protected function getOsInfo(): string
    {
        return $this->agent->platform() . ' ' . $this->agent->version($this->agent->platform());
    }

    /**
     * Get referral source
     */
    protected function getReferralSource(Request $request): ?string
    {
        // Check for referral in session (set during registration)
        if (session()->has('referral_source')) {
            return session('referral_source');
        }

        // Check for referral parameter
        if ($request->has('ref')) {
            return $request->input('ref');
        }

        // Check HTTP referrer
        $referrer = $request->header('referer');
        if ($referrer) {
            $parsed = parse_url($referrer);
            if (isset($parsed['host']) && $parsed['host'] !== $request->getHost()) {
                return $parsed['host'];
            }
        }

        return null;
    }

     /**
     * Get location from IP (optional - requires external service)
     */
    public function getLocationFromIp(string $ip): array
    {
        // You can integrate with services like:
        // - ipinfo.io
        // - ipapi.com
        // - freegeoip.app

        // Example with ipinfo.io (requires API key for production)
        try {
            $response = file_get_contents("http://ipinfo.io/{$ip}/json");
            $data = json_decode($response, true);

            if (isset($data['loc'])) {
                [$latitude, $longitude] = explode(',', $data['loc']);
                return [
                    'latitude' => (float) $latitude,
                    'longitude' => (float) $longitude,
                    'city' => $data['city'] ?? null,
                    'region' => $data['region'] ?? null,
                    'country' => $data['country'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            // Log error but don't fail the login process
            Log::info('Failed to get location from IP: ' . $e->getMessage());
        }

        return [];
    }
}


