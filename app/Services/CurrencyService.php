<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CurrencyService
{
    private $apiUrl = 'https://api.exchangerate-api.com/v4/latest/NGN';
    private $defaultCurrency = 'NGN';
    private $supportedCurrencies = ['NGN', 'USD'];
    private $currencySymbols = [
        'NGN' => '₦',
        'USD' => '$'
    ];

    // Cache key for exchange rates - avoid using tags
    private $cacheKey = 'currency_exchange_rates';

    /**
     * Get the user's currency based on geolocation or session preference
     *
     * @return string
     */
    public function getUserCurrency()
    {
        // Check if user has a currency preference in session
        if (Session::has('currency')) {
            return Session::get('currency');
        }

        // Get user country from GeoIP with error handling
        try {
            $location = geoip()->getLocation();
            $country = $location->iso_code ?? null;
        } catch (\Exception $e) {
            Log::warning('GeoIP error: ' . $e->getMessage());
            $country = null;
        }

        // Set currency based on country
        $currency = $this->defaultCurrency; // Default to NGN

        // If user is not in Nigeria, use USD
        if ($country && $country != 'NG') {
            $currency = 'USD';
        }

        // Store in session for future use
        $this->setUserCurrency($currency);

        return $currency;
    }

    /**
     * Set user currency preference in session
     *
     * @param string $currency
     * @return void
     */
    public function setUserCurrency(string $currency)
    {
        if (!in_array($currency, $this->supportedCurrencies)) {
            $currency = $this->defaultCurrency;
        }

        Session::put('currency', $currency);
        Session::put('currency_symbol', $this->currencySymbols[$currency]);
    }

    /**
     * Get exchange rates from API or cache
     *
     * @return array
     */
    public function getExchangeRates()
    {
        // Using a simple string key instead of tags
        return Cache::remember($this->cacheKey, 60 * 60, function () {
            try {
                $response = Http::timeout(5)->get($this->apiUrl);

                if ($response->successful()) {
                    $data = $response->json();

                    // Only extract the rates we need
                    return [
                        'NGN' => 1, // Base currency is always 1
                        'USD' => $data['rates']['USD'] ?? 0
                    ];
                }
            } catch (\Exception $e) {
                Log::error('Exchange rate API error: ' . $e->getMessage());
            }

            // Fallback rates if API fails
            return [
                'NGN' => 1,
                'USD' => 0.00067 // Default fallback rate (example value)
            ];
        });
    }

    /**
     * Clear the exchange rates cache
     *
     * @return bool
     */
    public function clearRatesCache()
    {
        return Cache::forget($this->cacheKey);
    }

    /**
     * Convert USD to Naira
     *
     * @param float $amount
     * @return float
     */
    public function convertUsdToNaira(float $amount)
    {
        $rates = $this->getExchangeRates();

        // Since our rates are based on NGN as base currency,
        // we need to divide by the USD rate to convert USD to NGN
        if (isset($rates['USD']) && $rates['USD'] > 0) {
            return $amount / $rates['USD'];
        }

        return $amount;
    }

    /**
     * Convert Naira to USD
     *
     * @param float $amount
     * @return float
     */
    public function convertNairaToUsd(float $amount)
    {
        $rates = $this->getExchangeRates();
        return $amount * ($rates['USD'] ?? 0.00067);
    }

    /**
     * Format price according to user's currency
     *
     * @param float $price
     * @param string|null $forceCurrency
     * @return array Contains 'value', 'formatted', 'currency', and 'symbol'
     */
    public function getFormattedPrice(float $price, string $forceCurrency = null)
    {
        $currency = $forceCurrency ?? $this->getUserCurrency();
        $convertedPrice = $price;

        // Convert price if needed
        if ($currency === 'USD' && $this->defaultCurrency === 'NGN') {
            $convertedPrice = $this->convertNairaToUsd($price);
        } elseif ($currency === 'NGN' && $this->defaultCurrency === 'USD') {
            $convertedPrice = $this->convertUsdToNaira($price);
        }

        $symbol = $this->currencySymbols[$currency] ?? '';

        return [
            'value' => $convertedPrice,
            'formatted' => $symbol . number_format($convertedPrice, 2),
            'currency' => $currency,
            'symbol' => $symbol
        ];
    }

    /**
     * Get all supported currencies
     *
     * @return array
     */
    public function getSupportedCurrencies()
    {
        return $this->supportedCurrencies;
    }

    /**
     * Get currency symbol
     *
     * @param string $currency
     * @return string
     */
    public function getCurrencySymbol(string $currency = null)
    {
        $currency = $currency ?? $this->getUserCurrency();
        return $this->currencySymbols[$currency] ?? '';
    }
}
