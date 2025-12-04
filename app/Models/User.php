<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'current_brand_id',
        'phone',
        'company',
        'timezone',
        'preferences',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the brands that the user belongs to.
     */
    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class)
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
     * Get the current active brand.
     */
    public function currentBrand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'current_brand_id');
    }

    /**
     * Get the API keys for the user.
     */
    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is owner of a brand.
     */
    public function isOwnerOf(Brand $brand): bool
    {
        return $this->brands()
            ->where('brand_id', $brand->id)
            ->wherePivot('role', 'owner')
            ->exists();
    }

    /**
     * Check if user has access to a brand.
     */
    public function hasAccessTo(Brand $brand): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->brands()->where('brand_id', $brand->id)->exists();
    }

    /**
     * Check if user can perform action in brand.
     */
    public function canInBrand(Brand $brand, string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $brand->userCan($this, $permission);
    }

    /**
     * Switch to a different brand.
     */
    public function switchToBrand(Brand $brand): bool
    {
        if (!$this->hasAccessTo($brand)) {
            return false;
        }

        $this->update(['current_brand_id' => $brand->id]);
        
        return true;
    }

    /**
     * Get user's role in specific brand.
     */
    public function roleIn(Brand $brand): ?string
    {
        $pivot = $this->brands()->where('brand_id', $brand->id)->first()?->pivot;
        
        return $pivot?->role;
    }

    /**
     * Record login.
     */
    public function recordLogin(string $ip): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    /**
     * Scope to filter active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }
}
