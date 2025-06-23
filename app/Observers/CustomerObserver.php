<?php

namespace App\Observers;

use App\Models\User;
use App\Models\CustomerActivityLog;
use App\Models\CustomerNotification;

class CustomerObserver
{


    /**
     * Handle the User "updated" event.
     */
     public function updated(User $user): void
    {
        $changes = $user->getChanges();
        $original = $user->getOriginal();

        // Skip if no actual changes
        if (empty($changes) || (count($changes) === 1 && isset($changes['updated_at']))) {
            return;
        }

        // Remove timestamps from changes for logging
        unset($changes['updated_at'], $changes['created_at']);

        // Check if password was changed
        if (isset($changes['password'])) {
            $this->logPasswordChange($user);
            $this->createPasswordChangeNotification($user);
            return;
        }

        // Check if email was changed
        if (isset($changes['email'])) {
            $this->logEmailChange($user, $original['email'], $changes['email']);
            $this->createEmailChangeNotification($user, $original['email']);
        }

        // Log general profile update
        if (!empty($changes)) {
            $this->logProfileUpdate($user, $changes);
        }
    }

   


    /**
     * Log password change
     */
    private function logPasswordChange(User $user): void
    {
        // Custom activity log
        CustomerActivityLog::log(
            userId: $user->id,
            activityType: 'password_changed',
            description: 'User changed their password',
            properties: [
                'event' => 'password_changed',
                'subject_type' => User::class,
                'subject_id' => $user->id,
            ]
        );

        // Spatie activity log
        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'event' => 'password_changed',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Password changed');
    }

     /**
     * Log email change
     */
    private function logEmailChange(User $user, string $oldEmail, string $newEmail): void
    {
        // Custom activity log
        CustomerActivityLog::log(
            userId: $user->id,
            activityType: 'email_changed',
            description: "Email changed from {$oldEmail} to {$newEmail}",
            properties: [
                'event' => 'email_changed',
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'old_email' => $oldEmail,
                'new_email' => $newEmail,
            ]
        );

        // Spatie activity log
        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'event' => 'email_changed',
                'old_email' => $oldEmail,
                'new_email' => $newEmail,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Email address changed');
    }

      /**
     * Log profile update
     */
    private function logProfileUpdate(User $user, array $changes): void
    {
        $changedFields = array_keys($changes);

        // Custom activity log
        CustomerActivityLog::log(
            userId: $user->id,
            activityType: 'profile_updated',
            description: 'Profile updated: ' . implode(', ', $changedFields),
            properties: [
                'event' => 'profile_updated',
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'changed_fields' => $changedFields,
                'changes' => $changes,
            ]
        );

        // Spatie activity log
        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'event' => 'profile_updated',
                'changed_fields' => $changedFields,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Profile updated');
    }

      /**
     * Create password change notification
     */
    private function createPasswordChangeNotification(User $user): void
    {
        CustomerNotification::create([
            'user_id' => $user->id,
            'title' => 'Password Changed',
            'message' => 'Your password has been successfully changed.',
            'notification_type' => 'security',
            'link_url' => route('user.security'),
        ]);
    }

    /**
     * Create email change notification
     */
    private function createEmailChangeNotification(User $user, string $oldEmail): void
    {
        CustomerNotification::create([
            'user_id' => $user->id,
            'title' => 'Email Address Changed',
            'message' => "Your email address has been changed from {$oldEmail} to {$user->email}.",
            'notification_type' => 'security',
            'link_url' => route('user.profile'),
        ]);
    }


}
