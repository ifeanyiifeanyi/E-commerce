<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VendorAdvertisement;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorAdvertisementPolicy
{
  use HandlesAuthorization;

  public function view(User $user, VendorAdvertisement $advertisement){
    return $user->hasRole(['vendor']) && $user->id === $advertisement->vendor_id;
  }


  public function update(User $user, VendorAdvertisement $advertisement)
    {
        return $user->hasRole(['vendor']) && $user->id === $advertisement->vendor_id;
    }

    public function approve(User $user, VendorAdvertisement $advertisement)
    {
        return $user->hasRole(['admin']);
    }

    public function reject(User $user, VendorAdvertisement $advertisement)
    {
        return $user->hasRole(['admin']);
    }
}
