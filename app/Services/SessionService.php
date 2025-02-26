<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Jenssegers\Agent\Agent;

class SessionService
{
    /**
     * Get all active sessions for the current user.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getActiveSessions()
    {
        $sessions = DB::table('sessions')
            ->where('user_id', request()->user()->id)
            ->get();

        $currentSessionId = session()->getId();
        $agent = new Agent();

        return $sessions->map(function ($session) use ($currentSessionId, $agent) {
            $agent->setUserAgent($session->user_agent);

            // Device detection
            $device = $agent->isPhone() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop');
            $deviceType = $agent->isPhone() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop');

            // Browser detection
            $browser = $agent->browser();

            // Location data (if available)
            $location = '';
            if (!empty($session->ip_address) && $session->ip_address !== '127.0.0.1') {
                try {
                    $geoData = geoip()->getLocation($session->ip_address);
                    $location = $geoData->city . ', ' . $geoData->country;
                } catch (\Exception $e) {
                    $location = 'Unknown';
                }
            } else {
                $location = 'Local';
            }

            return (object)[
                'id' => $session->id,
                'ip_address' => $session->ip_address,
                'device' => "$device - $browser",
                'device_type' => $deviceType,
                'location' => $location,
                'last_activity' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                'last_activity_date' => Carbon::createFromTimestamp($session->last_activity)->format('M d, Y h:i A'),
                'is_current' => $session->id === $currentSessionId
            ];
        });
    }

    /**
     * Get the last login information for a user.
     *
     * @param  int  $userId
     * @return object|null
     */
    public function getLastLogin($userId)
    {
        // Get the previous login (excluding current session)
        $currentSessionId = session()->getId();
        $lastSession = DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->orderBy('last_activity', 'desc')
            ->first();

        if (!$lastSession) {
            return null;
        }

        $agent = new Agent();
        $agent->setUserAgent($lastSession->user_agent);

        // Location data (if available)
        $location = '';
        if (!empty($lastSession->ip_address) && $lastSession->ip_address !== '127.0.0.1') {
            try {
                $geoData = geoip()->getLocation($lastSession->ip_address);
                $location = $geoData->city . ', ' . $geoData->country;
            } catch (\Exception $e) {
                $location = 'Unknown';
            }
        } else {
            $location = 'Local';
        }

        return (object)[
            'device' => $agent->isPhone() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop'),
            'browser' => $agent->browser(),
            'ip_address' => $lastSession->ip_address,
            'location' => $location,
            'date' => Carbon::createFromTimestamp($lastSession->last_activity)->format('M d, Y h:i A'),
            'time_ago' => Carbon::createFromTimestamp($lastSession->last_activity)->diffForHumans()
        ];
    }

    /**
     * Destroy a specific session.
     *
     * @param  string  $sessionId
     * @return bool
     */
    public function destroySession($sessionId): bool
    {
        // Only allow deleting sessions that belong to the current user
        return DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', request()->user()->id)
            ->delete() > 0;
    }
}
