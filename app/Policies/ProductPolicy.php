<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ProductPolicy
{
    /**
    * Determine whether the user can view any models.
    *
    * @param  \App\Models\User  $user
    * @return \Illuminate\Auth\Access\Response|bool
    */
   public function viewAny(User $user)
   {
       // Admins can view all products
       if ($user->hasRole('admin')) {
           return true;
       }

       // Vendors can view their products
       if ($user->hasRole('vendor')) {
           return true;
       }

       return false;
   }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Product $product)
    {
        // Admins can view any product
        if ($user->hasRole('admin')) {
            return true;
        }

        // Vendors can only view their own products
        if ($user->hasRole('vendor')) {
            return $product->vendor_id === $user->id;
        }

        return false;
    }

     /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // Both admins and vendors can create products
        return $user->hasRole('admin') || $user->hasRole('vendor');
    }

     /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Product $product)
    {
        // Admins can update any product
        if ($user->hasRole('admin')) {
            return true;
        }

        // Vendors can only update their own products
        if ($user->hasRole('vendor')) {
            return $product->vendor_id === $user->id;
        }

        return false;
    }

     /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Product $product)
    {
        // Admins can delete any product
        if ($user->hasRole('admin')) {
            return true;
        }

        // Vendors can only delete their own products
        if ($user->hasRole('vendor')) {
            return $product->vendor_id === $user->id;
        }

        return false;
    }

      /**
     * Determine whether the user can manage product images.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function manageImages(User $user, Product $product)
    {
        // Uses the same logic as update
        return $this->update($user, $product);
    }

      /**
     * Determine whether the user can toggle the product status.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function toggleStatus(User $user, Product $product)
    {
        // Uses the same logic as update
        return $this->update($user, $product);
    }
}
