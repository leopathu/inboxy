<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'brand_id',
        'user_id',
        'name',
        'key',
        'secret',
        'permissions',
        'allowed_ips',
        'requests_count',
        'daily_limit',
        'last_used_at',
        'last_used_ip',
        'is_active',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'permissions' => 'array',
        'allowed_ips' => 'array',
        'requests_count' => 'integer',
        'daily_limit' => 'integer',
        'last_used_at' => 'datetime',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'secret',
    ];

    /**
     * Get the brand that owns the API key.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the user that owns the API key.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new API key.
     */
    public static function generate(Brand $brand, User $user, ?string $name = null): self
    {
        return self::create([
            'brand_id' => $brand->id,
            'user_id' => $user->id,
            'name' => $name ?? 'API Key ' . now()->format('Y-m-d H:i:s'),
            'key' => 'ib_' . Str::random(40),
            'secret' => Str::random(32),
            'is_active' => true,
        ]);
    }

    /**
     * Check if the API key is valid and active.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if IP is allowed.
     */
    public function isIpAllowed(string $ip): bool
    {
        if (empty($this->allowed_ips)) {
            return true; // No restriction
        }

        return in_array($ip, $this->allowed_ips, true);
    }

    /**
     * Check if has specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if (empty($this->permissions)) {
            return true; // Full access
        }

        return in_array($permission, $this->permissions, true);
    }

    /**
     * Record API key usage.
     */
    public function recordUsage(string $ip): void
    {
        $this->update([
            'requests_count' => $this->requests_count + 1,
            'last_used_at' => now(),
            'last_used_ip' => $ip,
        ]);
    }

    /**
     * Scope to filter active API keys.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
}
