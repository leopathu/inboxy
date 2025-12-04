<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;

class BrandPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins can view all brands, users can view their own brands
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Brand $brand): bool
    {
        // Admins can view any brand, users can view brands they have access to
        return $user->isAdmin() || $user->hasAccessTo($brand);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins can create new brands
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Brand $brand): bool
    {
        // Admins can update any brand, owners can update their brands
        return $user->isAdmin() || $user->isOwnerOf($brand);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Brand $brand): bool
    {
        // Only admins can delete brands
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Brand $brand): bool
    {
        // Only admins can restore brands
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Brand $brand): bool
    {
        // Only admins can force delete brands
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can manage users for the brand.
     */
    public function manageUsers(User $user, Brand $brand): bool
    {
        // Admins, owners, or users with can_manage_users permission
        return $user->isAdmin() 
            || $user->isOwnerOf($brand) 
            || $user->canInBrand($brand, 'can_manage_users');
    }

    /**
     * Determine whether the user can manage settings for the brand.
     */
    public function manageSettings(User $user, Brand $brand): bool
    {
        // Admins, owners, or users with can_manage_settings permission
        return $user->isAdmin() 
            || $user->isOwnerOf($brand) 
            || $user->canInBrand($brand, 'can_manage_settings');
    }
}
