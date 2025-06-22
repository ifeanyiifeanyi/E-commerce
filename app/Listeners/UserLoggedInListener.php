<?php

namespace App\Listeners;

use Illuminate\Http\Request;
use App\Jobs\UpdateUserLoginInfo;
use Illuminate\Auth\Events\Login;
use App\Services\LoginTrackerService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserLoggedInListener
{
    /**
     * Create the event listener.
     */
   public function __construct(protected LoginTrackerService $loginTracker, protected Request $request)
    {

    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {

        $user = $event->user;

        // Collect login data
        $loginData = $this->loginTracker->collectLoginData($this->request);

        // If frontend didn't provide coordinates, try to get from IP
        if (empty($loginData['latitude']) || empty($loginData['longitude'])) {
            $locationData = $this->loginTracker->getLocationFromIp($loginData['ip']);
            if (!empty($locationData)) {
                $loginData = array_merge($loginData, $locationData);
            }
        }

        // Dispatch the job to update login information
        UpdateUserLoginInfo::dispatch($user->id, $loginData);
    }
}
