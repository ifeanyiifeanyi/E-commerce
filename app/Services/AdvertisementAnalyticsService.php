<?php

namespace App\Services;

use App\Models\VendorAdvertisement;
use App\Models\AdvertisementAnalytic;

class AdvertisementAnalyticsService
{
    /**
     * Get vendor advertisement statistics
     */
    public function getVendorStats(int $vendorId, int $days = 30): array
    {
        $advertisements = VendorAdvertisement::where('vendor_id', $vendorId)->get();
        
        $totalSpent = $advertisements->sum('amount_paid');
        $totalImpressions = $advertisements->sum('impressions');
        $totalClicks = $advertisements->sum('clicks');
        $averageCtr = $totalImpressions > 0 ? ($totalClicks / $totalImpressions) * 100 : 0;
        
        // Get daily analytics for the period
        $dailyAnalytics = AdvertisementAnalytic::whereIn('advertisement_id', $advertisements->pluck('id'))
            ->where('date', '>=', now()->subDays($days))
            ->selectRaw('date, SUM(impressions) as impressions, SUM(clicks) as clicks')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        return [
            'total_spent' => $totalSpent,
            'total_impressions' => $totalImpressions,
            'total_clicks' => $totalClicks,
            'average_ctr' => round($averageCtr, 2),
            'active_ads' => $advertisements->where('status', 'active')->count(),
            'daily_analytics' => $dailyAnalytics->toArray(),
        ];
    }
    
    /**
     * Get package performance statistics
     */
    public function getPackageStats( $packageId): array
    {
        $advertisements = VendorAdvertisement::where('package_id', $packageId)->get();
        
        return [
            'total_bookings' => $advertisements->count(),
            'active_bookings' => $advertisements->where('status', 'active')->count(),
            'total_revenue' => $advertisements->sum('amount_paid'),
            'average_ctr' => $advertisements->avg('ctr'),
            'total_impressions' => $advertisements->sum('impressions'),
            'total_clicks' => $advertisements->sum('clicks'),
        ];
    }
    
    /**
     * Get top performing advertisements
     */
    public function getTopPerformingAds(int $limit = 10): \Illuminate\Support\Collection
    {
        return VendorAdvertisement::with(['vendor', 'package'])
            ->where('status', 'active')
            ->orderByDesc('clicks')
            ->limit($limit)
            ->get();
    }
}
