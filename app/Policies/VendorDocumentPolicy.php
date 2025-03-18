<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VendorDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorDocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VendorDocument $document): bool
    {
        return $user->id === $document->user_id;
    }

     /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VendorDocument $document): bool
    {
        return $user->id === $document->user_id && $document->status !== 'approved';
    }
}
