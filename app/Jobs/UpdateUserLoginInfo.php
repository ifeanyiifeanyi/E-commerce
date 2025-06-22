<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateUserLoginInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $loginData;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, array $loginData)
    {
        $this->userId = $userId;
        $this->loginData = $loginData;
    }

    /**
     * Execute the job.
     */
   public function handle(): void
    {
        try {
            $user = User::find($this->userId);

            if (!$user) {
                Log::warning("User not found for login update: {$this->userId}");
                return;
            }

            // Prepare update data
            $updateData = [
                'last_login_at' => now(),
                'last_activity_at' => now(),
            ];

            // Add login tracking information
            if (isset($this->loginData['ip'])) {
                $updateData['last_login_ip'] = $this->loginData['ip'];
            }

            if (isset($this->loginData['device_info'])) {
                $updateData['device_info'] = $this->loginData['device_info'];
            }

            if (isset($this->loginData['browser_info'])) {
                $updateData['browser_info'] = $this->loginData['browser_info'];
            }

            if (isset($this->loginData['os_info'])) {
                $updateData['os_info'] = $this->loginData['os_info'];
            }

            if (isset($this->loginData['latitude'])) {
                $updateData['latitude'] = $this->loginData['latitude'];
            }

            if (isset($this->loginData['longitude'])) {
                $updateData['longitude'] = $this->loginData['longitude'];
            }

            // Update registration source and referral source only for users with 'user' role
            // and only if they don't already have these values
            if ($user->role === 'user') {
                if (empty($user->registration_source) && isset($this->loginData['registration_source'])) {
                    $updateData['registration_source'] = $this->loginData['registration_source'];
                }

                if (empty($user->referral_source) && isset($this->loginData['referral_source'])) {
                    $updateData['referral_source'] = $this->loginData['referral_source'];
                }
            }

            // Update the user
            $user->update($updateData);

            Log::info("Login info updated for user: {$this->userId}");

        } catch (\Exception $e) {
            Log::error("Failed to update login info for user {$this->userId}: " . $e->getMessage());

            // Optionally re-throw to trigger job retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("UpdateUserLoginInfo job failed for user {$this->userId}: " . $exception->getMessage());
    }

}
