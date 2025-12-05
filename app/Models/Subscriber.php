<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscribers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'list_id',
        'email',
        'first_name',
        'last_name',
        'custom_fields',
        'status',
        'ip_address',
        'country',
        'referrer',
        'confirmation_token',
        'unsubscribe_token',
        'confirmed_at',
        'subscribed_at',
        'unsubscribed_at',
        'bounced_at',
        'complained_at',
        'bounce_count',
        'bounce_type',
        'bounce_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'custom_fields' => 'array',
        'confirmed_at' => 'datetime',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'bounced_at' => 'datetime',
        'bounce_count' => 'integer',
    ];

    /**
     * Subscriber status constants.
     */
    public const STATUS_SUBSCRIBED = 'subscribed';
    public const STATUS_UNSUBSCRIBED = 'unsubscribed';
    public const STATUS_BOUNCED = 'bounced';
    public const STATUS_PENDING = 'pending_confirmation';
    public const STATUS_COMPLAINED = 'complained';

    /**
     * Bounce type constants.
     */
    public const BOUNCE_SOFT = 'soft';
    public const BOUNCE_HARD = 'hard';
    public const BOUNCE_COMPLAINT = 'complaint';

    /**
     * Get the list that owns the subscriber.
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(EmailList::class, 'list_id');
    }

    /**
     * Get custom field values for this subscriber.
     */
    public function customFieldValues(): HasMany
    {
        return $this->hasMany(SubscriberCustomFieldValue::class, 'subscriber_id');
    }

    /**
     * Get full name.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Check if subscriber is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_SUBSCRIBED;
    }

    /**
     * Check if subscriber needs confirmation.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Mark subscriber as subscribed.
     */
    public function subscribe(): void
    {
        $this->update([
            'status' => self::STATUS_SUBSCRIBED,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        $this->list->updateSubscriberCounts();
    }

    /**
     * Mark subscriber as unsubscribed.
     */
    public function unsubscribe(): void
    {
        $this->update([
            'status' => self::STATUS_UNSUBSCRIBED,
            'unsubscribed_at' => now(),
        ]);

        $this->list->updateSubscriberCounts();
    }

    /**
     * Confirm subscriber (from pending).
     */
    public function confirm(): void
    {
        $this->update([
            'status' => self::STATUS_SUBSCRIBED,
            'confirmed_at' => now(),
            'subscribed_at' => now(),
        ]);

        $this->list->updateSubscriberCounts();
    }

    /**
     * Record a bounce.
     */
    public function recordBounce(string $type = self::BOUNCE_SOFT): void
    {
        $bounceCount = $this->bounce_count + 1;
        $status = $this->status;

        // Hard bounce = immediate status change
        if ($type === self::BOUNCE_HARD) {
            $status = self::STATUS_BOUNCED;
        }
        // Soft bounce = change status after 3 bounces
        elseif ($type === self::BOUNCE_SOFT && $bounceCount >= 3) {
            $status = self::STATUS_BOUNCED;
        }

        $this->update([
            'bounce_count' => $bounceCount,
            'bounce_type' => $type,
            'bounced_at' => now(),
            'status' => $status,
        ]);

        if ($status === self::STATUS_BOUNCED) {
            $this->list->updateSubscriberCounts();
        }
    }

    /**
     * Mark as complained.
     */
    public function markAsComplained(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLAINED,
            'bounce_type' => self::BOUNCE_COMPLAINT,
            'bounced_at' => now(),
        ]);

        $this->list->updateSubscriberCounts();
    }

    /**
     * Scope for subscribed status only.
     */
    public function scopeSubscribed($query)
    {
        return $query->where('status', self::STATUS_SUBSCRIBED);
    }

    /**
     * Scope for pending confirmation.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for unsubscribed.
     */
    public function scopeUnsubscribed($query)
    {
        return $query->where('status', self::STATUS_UNSUBSCRIBED);
    }

    /**
     * Scope for bounced.
     */
    public function scopeBounced($query)
    {
        return $query->where('status', self::STATUS_BOUNCED);
    }

    /**
     * Generate a unique confirmation token.
     */
    public function generateConfirmationToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update(['confirmation_token' => $token]);
        return $token;
    }

    /**
     * Generate a unique unsubscribe token.
     */
    public function generateUnsubscribeToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update(['unsubscribe_token' => $token]);
        return $token;
    }

    /**
     * Find subscriber by confirmation token.
     */
    public static function findByConfirmationToken(string $token): ?self
    {
        return self::where('confirmation_token', $token)->first();
    }

    /**
     * Find subscriber by unsubscribe token.
     */
    public static function findByUnsubscribeToken(string $token): ?self
    {
        return self::where('unsubscribe_token', $token)->first();
    }

    /**
     * Get confirmation URL.
     */
    public function getConfirmationUrl(): string
    {
        if (!$this->confirmation_token) {
            $this->generateConfirmationToken();
        }
        
        return route('subscription.confirm', ['token' => $this->confirmation_token]);
    }

    /**
     * Get unsubscribe URL.
     */
    public function getUnsubscribeUrl(): string
    {
        if (!$this->unsubscribe_token) {
            $this->generateUnsubscribeToken();
        }
        
        return route('subscription.unsubscribe', ['token' => $this->unsubscribe_token]);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Generate tokens when creating a new subscriber
        static::creating(function ($subscriber) {
            if (!$subscriber->confirmation_token) {
                $subscriber->confirmation_token = bin2hex(random_bytes(32));
            }
            if (!$subscriber->unsubscribe_token) {
                $subscriber->unsubscribe_token = bin2hex(random_bytes(32));
            }
        });
    }
}
