<?php

namespace App\Observers;

use App\Models\CustomerAddress;
use App\Models\CustomerActivityLog;
use App\Models\CustomerNotification;

class CustomerAddressObserver
{
    /**
     * Handle the CustomerAddress "created" event.
     */

    public function created(CustomerAddress $address): void
    {
        // Custom activity log
        CustomerActivityLog::log(
            userId: $address->user_id,
            activityType: 'address_created',
            description: "New {$address->address_type} address added",
            properties: [
                'event' => 'address_created',
                'subject_type' => CustomerAddress::class,
                'subject_id' => $address->id,
                'address_type' => $address->address_type,
                'city' => $address->city,
                'state' => $address->state,
                'country' => $address->country,
                'is_default' => $address->is_default,
            ]
        );

        // Spatie activity log
        activity()
            ->causedBy($address->user)
            ->performedOn($address)
            ->withProperties([
                'event' => 'address_created',
                'address_type' => $address->address_type,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('New address added');

        // Create notification
        CustomerNotification::create([
            'user_id' => $address->user_id,
            'title' => 'New Address Added',
            'message' => "A new {$address->address_type} address has been added to your account.",
            'notification_type' => 'system',
            'link_url' => route('user.addresses'),
        ]);
    }

    /**
     * Handle the CustomerAddress "updated" event.
     */
     /**
     * Handle the CustomerAddress "updated" event.
     */
    public function updated(CustomerAddress $address): void
    {
        $changes = $address->getChanges();
        $original = $address->getOriginal();

        // Skip if no actual changes
        if (empty($changes) || (count($changes) === 1 && isset($changes['updated_at']))) {
            return;
        }

        // Remove timestamps from changes for logging
        unset($changes['updated_at'], $changes['created_at']);

        if (empty($changes)) {
            return;
        }

        $changedFields = array_keys($changes);

        // Custom activity log
        CustomerActivityLog::log(
            userId: $address->user_id,
            activityType: 'address_updated',
            description: "Address updated: " . implode(', ', $changedFields),
            properties: [
                'event' => 'address_updated',
                'subject_type' => CustomerAddress::class,
                'subject_id' => $address->id,
                'changed_fields' => $changedFields,
                'changes' => $changes,
                'original' => array_intersect_key($original, $changes),
            ]
        );

        // Spatie activity log
        activity()
            ->causedBy($address->user)
            ->performedOn($address)
            ->withProperties([
                'event' => 'address_updated',
                'changed_fields' => $changedFields,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Address updated');

        // Create notification for significant changes
        if (array_intersect($changedFields, ['address_line1', 'city', 'state', 'country', 'postal_code'])) {
            CustomerNotification::create([
                'user_id' => $address->user_id,
                'title' => 'Address Updated',
                'message' => "Your {$address->address_type} address has been updated.",
                'notification_type' => 'system',
                'link_url' => route('user.addresses'),
            ]);
        }
    }


    /**
     * Handle the CustomerAddress "deleted" event.
     */
    public function deleting(CustomerAddress $address): void
    {
        // Custom activity log
        CustomerActivityLog::log(
            userId: $address->user_id,
            activityType: 'address_deleted',
            description: "Deleted {$address->address_type} address",
            properties: [
                'event' => 'address_deleted',
                'subject_type' => CustomerAddress::class,
                'subject_id' => $address->id,
                'address_type' => $address->address_type,
                'city' => $address->city,
                'state' => $address->state,
                'country' => $address->country,
                'deleted_address' => $address->toArray(),
            ]
        );

        // Spatie activity log
        activity()
            ->causedBy($address->user)
            ->performedOn($address)
            ->withProperties([
                'event' => 'address_deleted',
                'address_type' => $address->address_type,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('Address deleted');

        // Create notification
        CustomerNotification::create([
            'user_id' => $address->user_id,
            'title' => 'Address Deleted',
            'message' => "Your {$address->address_type} address has been removed from your account.",
            'notification_type' => 'system',
            'link_url' => route('user.addresses'),
        ]);
    }

    /**
     * Handle the CustomerAddress "restored" event.
     */
    public function restored(CustomerAddress $customerAddress): void
    {
        //
    }

    /**
     * Handle the CustomerAddress "force deleted" event.
     */
    public function forceDeleted(CustomerAddress $customerAddress): void
    {
        //
    }
}
