<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'company',
        'email',
        'website',
        'address',
        'country',
        'phone',
        'logo',
        'primary_color',
        'monthly_send_limit',
        'emails_sent_this_month',
        'limit_reset_date',
        'cost_per_email',
        'aws_access_key_id',
        'aws_secret_access_key',
        'aws_region',
        'use_own_ses',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'use_own_smtp',
        'from_name',
        'from_email',
        'reply_to_email',
        'can_create_lists',
        'can_create_campaigns',
        'can_import_subscribers',
        'can_export_data',
        'can_view_reports',
        'is_active',
        'last_login_at',
        'notes',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'monthly_send_limit' => 'integer',
        'emails_sent_this_month' => 'integer',
        'limit_reset_date' => 'date',
        'cost_per_email' => 'decimal:6',
        'smtp_port' => 'integer',
        'use_own_ses' => 'boolean',
        'use_own_smtp' => 'boolean',
        'can_create_lists' => 'boolean',
        'can_create_campaigns' => 'boolean',
        'can_import_subscribers' => 'boolean',
        'can_export_data' => 'boolean',
        'can_view_reports' => 'boolean',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'settings' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'aws_access_key_id',
        'aws_secret_access_key',
        'smtp_password',
    ];

    /**
     * Get the users that belong to the brand.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot([
                'role',
                'can_manage_lists',
                'can_manage_campaigns',
                'can_manage_subscribers',
                'can_view_reports',
                'can_manage_settings',
                'can_manage_users',
            ])
            ->withTimestamps();
    }

    /**
     * Get the lists for this brand.
     */
    public function lists(): HasMany
    {
        return $this->hasMany(EmailList::class);
    }

    /**
     * Get the campaigns for this brand.
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * Get the API keys for this brand.
     */
    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    /**
     * Get the subscription forms for this brand.
     */
    public function subscriptionForms(): HasMany
    {
        return $this->hasMany(SubscriptionForm::class);
    }

    /**
     * Check if brand has reached monthly send limit.
     */
    public function hasReachedSendLimit(): bool
    {
        if ($this->monthly_send_limit === 0) {
            return false; // Unlimited
        }

        return $this->emails_sent_this_month >= $this->monthly_send_limit;
    }

    /**
     * Get remaining sends for the month.
     */
    public function getRemainingsendsAttribute(): int
    {
        if ($this->monthly_send_limit === 0) {
            return PHP_INT_MAX; // Unlimited
        }

        return max(0, $this->monthly_send_limit - $this->emails_sent_this_month);
    }

    /**
     * Increment email send count.
     */
    public function incrementEmailsSent(int $count = 1): void
    {
        $this->increment('emails_sent_this_month', $count);
    }

    /**
     * Reset monthly send count.
     */
    public function resetMonthlySendCount(): void
    {
        $this->update([
            'emails_sent_this_month' => 0,
            'limit_reset_date' => now()->addMonth(),
        ]);
    }

    /**
     * Check if user has specific permission in this brand.
     */
    public function userCan(User $user, string $permission): bool
    {
        $pivot = $this->users()->where('user_id', $user->id)->first()?->pivot;
        
        if (!$pivot) {
            return false;
        }

        // Owner has all permissions
        if ($pivot->role === 'owner') {
            return true;
        }

        return $pivot->{$permission} ?? false;
    }

    /**
     * Scope to filter active brands.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
