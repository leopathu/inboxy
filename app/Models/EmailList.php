<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailList extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'brand_id',
        'name',
        'description',
        'from_name',
        'from_email',
        'reply_to_email',
        'subscribe_success_message',
        'unsubscribe_success_message',
        'confirmation_email_subject',
        'confirmation_email_body',
        'require_confirmation',
        'send_welcome_email',
        'welcome_email_subject',
        'welcome_email_body',
        'subscriber_count',
        'active_subscriber_count',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'require_confirmation' => 'boolean',
        'send_welcome_email' => 'boolean',
        'is_active' => 'boolean',
        'subscriber_count' => 'integer',
        'active_subscriber_count' => 'integer',
    ];

    /**
     * Get the brand that owns the list.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the subscribers for this list.
     */
    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscriber::class, 'list_id');
    }

    /**
     * Get active subscribers only.
     */
    public function activeSubscribers(): HasMany
    {
        return $this->subscribers()->where('status', 'subscribed');
    }

    /**
     * Get custom fields for this list.
     */
    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class, 'list_id');
    }

    /**
     * Update subscriber counts.
     */
    public function updateSubscriberCounts(): void
    {
        $this->subscriber_count = $this->subscribers()->count();
        $this->active_subscriber_count = $this->activeSubscribers()->count();
        $this->saveQuietly();
    }

    /**
     * Scope for active lists only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
