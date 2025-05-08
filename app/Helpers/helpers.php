<?php

if (!function_exists('format_price')) {
    /**
     * Format price according to user's location
     *
     * @param float $nairaPrice
     * @return string
     */
    function format_price($nairaPrice)
    {
        $currencyService = app(App\Services\CurrencyService::class);
        $priceData = $currencyService->getFormattedPrice($nairaPrice);

        return $priceData['formatted'];
    }
}

if (!function_exists('get_price_in_user_currency')) {
    /**
     * Get price in user's preferred currency
     *
     * @param float $nairaPrice
     * @return float
     */
    function get_price_in_user_currency($nairaPrice)
    {
        $currencyService = app(App\Services\CurrencyService::class);
        $priceData = $currencyService->getFormattedPrice($nairaPrice);

        return $priceData['amount'];
    }
}

if (!function_exists('get_currency_symbol')) {
    /**
     * Get currency symbol based on user's location
     *
     * @return string
     */
    function get_currency_symbol()
    {
        return session('currency_symbol', '₦');
    }
}
