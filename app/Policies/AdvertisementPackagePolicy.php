<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AdvertisementPackage;

class AdvertisementPackagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdvertisementPackage $advertisementPackage): bool
    {
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AdvertisementPackage $advertisementPackage): bool
    {
        // Only allow updates if there are no active subscriptions
        if ($advertisementPackage->hasActiveSubscriptions()) {
            return false;
        }
        
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdvertisementPackage $advertisementPackage): bool
    {
        // Cannot delete packages with any subscriptions (active or expired)
        if ($advertisementPackage->hasAnySubscriptions()) {
            return false;
        }
        
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AdvertisementPackage $advertisementPackage): bool
    {
        return $user->hasRole(['super-admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AdvertisementPackage $advertisementPackage): bool
    {
        return $user->hasRole(['super-admin']);
    }

    /**
     * Determine whether the user can toggle package status.
     */
    public function toggleStatus(User $user, AdvertisementPackage $advertisementPackage): bool
    {
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can send notifications about packages.
     */
    public function sendNotifications(User $user): bool
    {
        return $user->hasRole(['admin']);
    }
}