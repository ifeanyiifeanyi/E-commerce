<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class LocationService
{
    private $client;
    private $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.ipstack.key');
    }

    public function getLocationByIp($ipAddress = null): array
    {
        $ipAddress = $ipAddress ?? request()->ip();

        // Skip for localhost testing
        if ($ipAddress === '127.0.0.1' || $ipAddress === '::1') {
            return $this->getDefaultLocation();
        }

        try {
            $response = $this->client->get("http://api.ipstack.com/{$ipAddress}", [
                'query' => [
                    'access_key' => $this->apiKey,
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (!empty($data) && isset($data['latitude'])) {
                return [
                    'ip_address' => $data['ip'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'location_city' => $data['city'],
                    'location_state' => $data['region_name'],
                    'location_country' => $data['country_name'],
                    'location_country_code' => $data['country_code'],
                    'postal_code' => $data['zip'],
                    'continent' => $data['continent_name'],
                    'timezone' => $data['time_zone']['id'] ?? null,
                ];
            }
        } catch (Exception $e) {
            Log::error('Error getting location information: ' . $e->getMessage());
        }

        return $this->getDefaultLocation();
    }

    private function getDefaultLocation(): array
    {
        return [
            'ip_address' => request()->ip(),
            'latitude' => null,
            'longitude' => null,
            'location_city' => null,
            'location_state' => null,
            'location_country' => null,
            'location_country_code' => null,
            'postal_code' => null,
            'continent' => null,
            'timezone' => null,
        ];
    }
}
