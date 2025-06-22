<?php

namespace App\Services;

use App\Models\User;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\CustomerLoginHistory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;


class LoginHistoryService
{
    /**
     * Log a login attempt for a user.
     *
     * @param User|null $user The user who attempted to log in (null for failed attempts)
     * @param Request $request The HTTP request containing IP and user agent
     * @param bool $successful Whether the login attempt was successful
     * @param string|null $failureReason Reason for login failure, if applicable
     * @return CustomerLoginHistory
     */
    public function logLoginAttempt(?User $user, Request $request, bool $successful = true, ?string $failureReason = null): CustomerLoginHistory
    {
        $deviceInfo = $this->getDeviceInfo($request->userAgent());
        $locationData = $this->getLocationData($request->ip());

        return CustomerLoginHistory::create([
            'user_id' => $user?->id,
            'ip_address' => $request->ip(),
            'device_type' => $deviceInfo['device_type'],
            'device_name' => $deviceInfo['device_name'],
            'browser' => $deviceInfo['browser'],
            'browser_version' => $deviceInfo['browser_version'],
            'operating_system' => $deviceInfo['operating_system'],
            'os_version' => $deviceInfo['os_version'],
            'user_agent' => $request->userAgent(),
            'location_city' => $locationData['city'] ?? null,
            'location_state' => $locationData['state'] ?? null,
            'location_country' => $locationData['country'] ?? null,
            'latitude' => $locationData['latitude'] ?? null,
            'longitude' => $locationData['longitude'] ?? null,
            'successful' => $successful,
            'failure_reason' => $failureReason,
        ]);
    }



    /**
     * Get device information from user agent
     */
    protected function getDeviceInfo(string $userAgent): array
    {
        $userAgent = strtolower($userAgent);

        return [
            'device_type' => $this->getDeviceType($userAgent),
            'device_name' => $this->getDeviceName($userAgent),
            'browser' => $this->getBrowser($userAgent),
            'browser_version' => $this->getBrowserVersion($userAgent),
            'operating_system' => $this->getOperatingSystem($userAgent),
            'os_version' => $this->getOSVersion($userAgent),
        ];
    }

    protected function getDeviceType(string $userAgent): string
    {
        if (preg_match('/mobile|android|iphone|ipod|blackberry|windows phone/', $userAgent)) {
            return 'mobile';
        }
        if (preg_match('/tablet|ipad/', $userAgent)) {
            return 'tablet';
        }
        return 'desktop';
    }


    protected function getDeviceName(string $userAgent): string
    {
        if (preg_match('/iphone/', $userAgent)) return 'iPhone';
        if (preg_match('/ipad/', $userAgent)) return 'iPad';
        if (preg_match('/android/', $userAgent)) {
            if (preg_match('/android.*;\s*([^)]+)\)/', $userAgent, $matches)) {
                $device = trim($matches[1]);
                $device = preg_replace('/build\/.*$/i', '', $device);
                return ucwords(trim($device)) ?: 'Android Device';
            }
            return 'Android Device';
        }
        if (preg_match('/windows phone/', $userAgent)) return 'Windows Phone';
        if (preg_match('/blackberry/', $userAgent)) return 'BlackBerry';
        return 'Computer';
    }


    protected function getBrowser(string $userAgent): string
    {
        $browsers = [
            'Firefox' => 'firefox',
            'Chrome' => 'chrome',
            'Safari' => 'safari',
            'Opera' => 'opera|opr',
            'Edge' => 'edge|edg',
            'Internet Explorer' => 'msie|trident',
            'Samsung Browser' => 'samsungbrowser',
            'UC Browser' => 'ucbrowser',
        ];

        foreach ($browsers as $browser => $pattern) {
            if (preg_match('/(' . $pattern . ')/', $userAgent)) {
                return $browser;
            }
        }
        return 'Unknown Browser';
    }

    protected function getBrowserVersion(string $userAgent): string
    {
        if (preg_match('/chrome\/([0-9.]+)/', $userAgent, $matches)) return $matches[1];
        if (preg_match('/firefox\/([0-9.]+)/', $userAgent, $matches)) return $matches[1];
        if (preg_match('/version\/([0-9.]+).*safari/', $userAgent, $matches)) return $matches[1];
        if (preg_match('/edge?\/([0-9.]+)/', $userAgent, $matches)) return $matches[1];
        if (preg_match('/(?:opera|opr)\/([0-9.]+)/', $userAgent, $matches)) return $matches[1];
        if (preg_match('/msie ([0-9.]+)/', $userAgent, $matches)) return $matches[1];
        if (preg_match('/samsungbrowser\/([0-9.]+)/', $userAgent, $matches)) return $matches[1];
        return 'Unknown';
    }

    protected function getOperatingSystem(string $userAgent): string
    {
        $systems = [
            'Windows 11' => 'windows nt 10.0.*; win64.*; x64.*; .*edg\/',
            'Windows 10' => 'windows nt 10.0',
            'Windows 8.1' => 'windows nt 6.3',
            'Windows 8' => 'windows nt 6.2',
            'Windows 7' => 'windows nt 6.1',
            'macOS' => 'mac os x|macos',
            'iOS' => 'iphone os|ios',
            'Android' => 'android',
            'Linux' => 'linux',
            'Ubuntu' => 'ubuntu',
            'Chrome OS' => 'cros',
        ];

        foreach ($systems as $system => $pattern) {
            if (preg_match('/' . $pattern . '/', $userAgent)) {
                return $system;
            }
        }
        return 'Unknown OS';
    }

    protected function getOSVersion(string $userAgent): string
    {
        if (preg_match('/windows nt ([0-9.]+)/', $userAgent, $matches)) {
            $version = $matches[1];
            $windowsVersions = [
                '10.0' => '10/11',
                '6.3' => '8.1',
                '6.2' => '8',
                '6.1' => '7',
            ];
            return $windowsVersions[$version] ?? $version;
        }
        if (preg_match('/mac os x ([0-9_]+)/', $userAgent, $matches)) {
            return str_replace('_', '.', $matches[1]);
        }
        if (preg_match('/(?:iphone os|ios) ([0-9_]+)/', $userAgent, $matches)) {
            return str_replace('_', '.', $matches[1]);
        }
        if (preg_match('/android ([0-9.]+)/', $userAgent, $matches)) {
            return $matches[1];
        }
        return 'Unknown';
    }

    /**
     * Get location data from IP address with multiple fallback services
     */
    protected function getLocationData(string $ip): array
    {
        // Skip local/private IPs
        if ($this->isLocalOrPrivateIp($ip)) {
            return $this->getDefaultLocation();
        }

        // Check cache first
        $cacheKey = "location_ip_{$ip}";
        $cachedLocation = Cache::get($cacheKey);

        if ($cachedLocation) {
            return $cachedLocation;
        }

        // Try different services in order
        $location = $this->tryIpApiService($ip) ?:
            $this->tryIpInfoService($ip) ?:
            $this->getDefaultLocation();

        // Cache the result for 24 hours
        Cache::put($cacheKey, $location, now()->addHours(24));

        return $location;
    }

    protected function tryIpApiService(string $ip): ?array
    {
        try {
            $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}");

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'success') {
                    return [
                        'city' => $data['city'] ?? null,
                        'state' => $data['regionName'] ?? null,
                        'country' => $data['country'] ?? null,
                        'country_code' => $data['countryCode'] ?? null,
                        'latitude' => $data['lat'] ?? null,
                        'longitude' => $data['lon'] ?? null,
                        'timezone' => $data['timezone'] ?? null,
                        'isp' => $data['isp'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("IP-API service failed for IP {$ip}: " . $e->getMessage());
        }
        return null;
    }

    protected function tryIpInfoService(string $ip): ?array
    {
        try {
            $response = Http::timeout(5)->get("https://ipinfo.io/{$ip}/json");

            if ($response->successful()) {
                $data = $response->json();

                if (!isset($data['error'])) {
                    $coordinates = isset($data['loc']) ? explode(',', $data['loc']) : [null, null];

                    return [
                        'city' => $data['city'] ?? null,
                        'state' => $data['region'] ?? null,
                        'country' => $data['country'] ?? null,
                        'country_code' => $data['country'] ?? null,
                        'latitude' => $coordinates[0] ?? null,
                        'longitude' => $coordinates[1] ?? null,
                        'timezone' => $data['timezone'] ?? null,
                        'isp' => $data['org'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning("IPInfo service failed for IP {$ip}: " . $e->getMessage());
        }
        return null;
    }

    protected function isLocalOrPrivateIp(string $ip): bool
    {
        $privateRanges = [
            '127.0.0.0/8',    // Loopback
            '10.0.0.0/8',     // Private Class A
            '172.16.0.0/12',  // Private Class B
            '192.168.0.0/16', // Private Class C
            '169.254.0.0/16', // Link-local
        ];

        foreach ($privateRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return in_array($ip, ['localhost', '127.0.0.1', '::1']);
    }

    protected function ipInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet = $subnet & $mask;

        return ($ip & $mask) === $subnet;
    }

    protected function getDefaultLocation(): array
    {
        return [
            'city' => 'Unknown',
            'state' => 'Unknown',
            'country' => 'Unknown',
            'country_code' => null,
            'latitude' => null,
            'longitude' => null,
            'timezone' => null,
            'isp' => null,
        ];
    }
}
