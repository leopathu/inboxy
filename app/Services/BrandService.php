<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BrandService
{
    /**
     * Get paginated brands.
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Brand::with(['users'])
            ->withCount(['users'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get all active brands.
     */
    public function getActive(): Collection
    {
        return Brand::active()
            ->orderBy('name')
            ->get();
    }

    /**
     * Get brands for a specific user.
     */
    public function getUserBrands(User $user): Collection
    {
        if ($user->isAdmin()) {
            return $this->getActive();
        }

        return $user->brands()
            ->active()
            ->withPivot('role')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new brand.
     */
    public function create(array $data, ?User $owner = null): Brand
    {
        return DB::transaction(function () use ($data, $owner) {
            // Create brand
            $brand = Brand::create([
                'name' => $data['name'],
                'company' => $data['company'] ?? null,
                'email' => $data['email'] ?? null,
                'website' => $data['website'] ?? null,
                'address' => $data['address'] ?? null,
                'country' => $data['country'] ?? null,
                'phone' => $data['phone'] ?? null,
                'primary_color' => $data['primary_color'] ?? '#3490dc',
                'monthly_send_limit' => $data['monthly_send_limit'] ?? 0,
                'cost_per_email' => $data['cost_per_email'] ?? 0,
                'from_name' => $data['from_name'] ?? null,
                'from_email' => $data['from_email'] ?? null,
                'reply_to_email' => $data['reply_to_email'] ?? null,
                'can_create_lists' => $data['can_create_lists'] ?? true,
                'can_create_campaigns' => $data['can_create_campaigns'] ?? true,
                'can_import_subscribers' => $data['can_import_subscribers'] ?? true,
                'can_export_data' => $data['can_export_data'] ?? true,
                'can_view_reports' => $data['can_view_reports'] ?? true,
                'is_active' => $data['is_active'] ?? true,
                'notes' => $data['notes'] ?? null,
                'settings' => $data['settings'] ?? [],
            ]);

            // Attach owner if provided
            if ($owner) {
                $brand->users()->attach($owner->id, [
                    'role' => 'owner',
                    'can_manage_lists' => true,
                    'can_manage_campaigns' => true,
                    'can_manage_subscribers' => true,
                    'can_view_reports' => true,
                    'can_manage_settings' => true,
                    'can_manage_users' => true,
                ]);

                // Set as current brand if user has no current brand
                if (!$owner->current_brand_id) {
                    $owner->update(['current_brand_id' => $brand->id]);
                }
            }

            return $brand->load('users');
        });
    }

    /**
     * Update a brand.
     */
    public function update(Brand $brand, array $data): Brand
    {
        $brand->update([
            'name' => $data['name'] ?? $brand->name,
            'company' => $data['company'] ?? $brand->company,
            'email' => $data['email'] ?? $brand->email,
            'website' => $data['website'] ?? $brand->website,
            'address' => $data['address'] ?? $brand->address,
            'country' => $data['country'] ?? $brand->country,
            'phone' => $data['phone'] ?? $brand->phone,
            'logo' => $data['logo'] ?? $brand->logo,
            'primary_color' => $data['primary_color'] ?? $brand->primary_color,
            'monthly_send_limit' => $data['monthly_send_limit'] ?? $brand->monthly_send_limit,
            'cost_per_email' => $data['cost_per_email'] ?? $brand->cost_per_email,
            'from_name' => $data['from_name'] ?? $brand->from_name,
            'from_email' => $data['from_email'] ?? $brand->from_email,
            'reply_to_email' => $data['reply_to_email'] ?? $brand->reply_to_email,
            'can_create_lists' => $data['can_create_lists'] ?? $brand->can_create_lists,
            'can_create_campaigns' => $data['can_create_campaigns'] ?? $brand->can_create_campaigns,
            'can_import_subscribers' => $data['can_import_subscribers'] ?? $brand->can_import_subscribers,
            'can_export_data' => $data['can_export_data'] ?? $brand->can_export_data,
            'can_view_reports' => $data['can_view_reports'] ?? $brand->can_view_reports,
            'is_active' => $data['is_active'] ?? $brand->is_active,
            'notes' => $data['notes'] ?? $brand->notes,
        ]);

        return $brand->fresh();
    }

    /**
     * Update SES configuration.
     */
    public function updateSesConfig(Brand $brand, array $data): Brand
    {
        $brand->update([
            'aws_access_key_id' => $data['aws_access_key_id'] ?? $brand->aws_access_key_id,
            'aws_secret_access_key' => $data['aws_secret_access_key'] ?? $brand->aws_secret_access_key,
            'aws_region' => $data['aws_region'] ?? $brand->aws_region,
            'use_own_ses' => $data['use_own_ses'] ?? $brand->use_own_ses,
        ]);

        return $brand->fresh();
    }

    /**
     * Update SMTP configuration.
     */
    public function updateSmtpConfig(Brand $brand, array $data): Brand
    {
        $brand->update([
            'smtp_host' => $data['smtp_host'] ?? $brand->smtp_host,
            'smtp_port' => $data['smtp_port'] ?? $brand->smtp_port,
            'smtp_username' => $data['smtp_username'] ?? $brand->smtp_username,
            'smtp_password' => $data['smtp_password'] ?? $brand->smtp_password,
            'smtp_encryption' => $data['smtp_encryption'] ?? $brand->smtp_encryption,
            'use_own_smtp' => $data['use_own_smtp'] ?? $brand->use_own_smtp,
        ]);

        return $brand->fresh();
    }

    /**
     * Delete a brand.
     */
    public function delete(Brand $brand): bool
    {
        return $brand->delete();
    }

    /**
     * Add user to brand.
     */
    public function addUser(Brand $brand, User $user, string $role = 'user', array $permissions = []): void
    {
        $defaultPermissions = [
            'can_manage_lists' => true,
            'can_manage_campaigns' => true,
            'can_manage_subscribers' => true,
            'can_view_reports' => true,
            'can_manage_settings' => $role === 'owner' || $role === 'manager',
            'can_manage_users' => $role === 'owner',
        ];

        $brand->users()->attach($user->id, array_merge(
            ['role' => $role],
            $defaultPermissions,
            $permissions
        ));
    }

    /**
     * Update user permissions in brand.
     */
    public function updateUserPermissions(Brand $brand, User $user, array $permissions): void
    {
        $brand->users()->updateExistingPivot($user->id, $permissions);
    }

    /**
     * Remove user from brand.
     */
    public function removeUser(Brand $brand, User $user): void
    {
        $brand->users()->detach($user->id);

        // Clear current brand if it was this one
        if ($user->current_brand_id === $brand->id) {
            $newBrand = $user->brands()->first();
            $user->update(['current_brand_id' => $newBrand?->id]);
        }
    }

    /**
     * Get brand statistics.
     */
    public function getStatistics(Brand $brand): array
    {
        try {
            $totalLists = $brand->lists()->count();
            $totalCampaigns = $brand->campaigns()->count();
            $totalSubscribers = 0; // Will be implemented when subscribers table exists
        } catch (\Exception $e) {
            // Tables don't exist yet
            $totalLists = 0;
            $totalCampaigns = 0;
            $totalSubscribers = 0;
        }

        return [
            'total_lists' => $totalLists,
            'total_campaigns' => $totalCampaigns,
            'total_subscribers' => $totalSubscribers,
            'emails_sent_this_month' => $brand->emails_sent_this_month,
            'remaining_sends' => $brand->remaining_sends,
            'send_limit_percentage' => $brand->monthly_send_limit > 0 
                ? ($brand->emails_sent_this_month / $brand->monthly_send_limit) * 100 
                : 0,
        ];
    }

    /**
     * Check and reset monthly limits if needed.
     */
    public function checkAndResetLimits(Brand $brand): void
    {
        if ($brand->limit_reset_date && $brand->limit_reset_date->isPast()) {
            $brand->resetMonthlySendCount();
        }
    }
}
