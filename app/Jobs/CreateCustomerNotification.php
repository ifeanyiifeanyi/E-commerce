<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use App\Models\CustomerLoginHistory;
use App\Models\CustomerNotification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateCustomerNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
     protected $loginHistory;

    public function __construct(CustomerLoginHistory $loginHistory)
    {
        $this->loginHistory = $loginHistory;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Only create basic login notifications, security alerts are handled in observer
            if ($this->shouldCreateBasicNotification()) {
                $this->createBasicLoginNotification();
            }

            // Log successful job completion
            Log::info('Login notification job completed', [
                'login_history_id' => $this->loginHistory->id,
                'user_id' => $this->loginHistory->user_id,
                'successful' => $this->loginHistory->successful,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create login notification', [
                'login_history_id' => $this->loginHistory->id,
                'user_id' => $this->loginHistory->user_id,
                'error' => $e->getMessage(),
            ]);

            // Re-throw to mark job as failed
            throw $e;
        }
    }

     /**
     * Determine if we should create a basic notification
     */
    protected function shouldCreateBasicNotification(): bool
    {
        // Don't create notifications for failed logins (handled separately)
        if (!$this->loginHistory->successful) {
            return false;
        }

        // Don't create notifications if user doesn't exist
        if (!$this->loginHistory->user) {
            return false;
        }

        // Create notification for successful logins
        return true;
    }

    /**
     * Create basic login notification
     */
    protected function createBasicLoginNotification(): void
    {
        $user = $this->loginHistory->user;
        $location = $this->loginHistory->getLocationAttribute();
        $device = $this->loginHistory->getDeviceInfoAttribute();
        $time = $this->loginHistory->created_at->format('M j, Y \a\t g:i A');

        CustomerNotification::create([
            'user_id' => $user->id,
            'title' => 'Successful Login',
            'message' => "You successfully logged in from {$location} using {$device} on {$time}.",
            'notification_type' => 'security',
            'link_url' => route('user.security'),
        ]);
    }
}
