<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    // Campaign Types
    const TYPE_REGULAR = 'regular';
    const TYPE_AUTORESPONDER = 'autoresponder';
    const TYPE_TRIGGER = 'trigger';

    // Campaign Status
    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_SENDING = 'sending';
    const STATUS_SENT = 'sent';
    const STATUS_PAUSED = 'paused';
    const STATUS_CANCELLED = 'cancelled';

    // Send To Options
    const SEND_TO_ALL = 'all';
    const SEND_TO_SUBSCRIBED = 'subscribed';
    const SEND_TO_UNCONFIRMED = 'unconfirmed';

    protected $fillable = [
        'brand_id',
        'user_id',
        'list_id',
        'template_id',
        'type',
        'name',
        'subject',
        'from_name',
        'from_email',
        'reply_to_email',
        'html_content',
        'plain_text_content',
        'attachments',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'total_recipients',
        'emails_sent',
        'track_opens',
        'track_clicks',
        'segments',
        'send_to',
        'throttle_rate',
        'enable_throttle',
        'test_emails',
        'delay_value',
        'delay_unit',
        'trigger_date',
        'trigger_event',
        'is_active',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
        'segments' => 'array',
        'attachments' => 'array',
        'enable_throttle' => 'boolean',
        'is_active' => 'boolean',
        'trigger_date' => 'datetime',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function list(): BelongsTo
    {
        return $this->belongsTo(EmailList::class, 'list_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function sends(): HasMany
    {
        return $this->hasMany(CampaignSend::class);
    }

    public function opens(): HasMany
    {
        return $this->hasMany(CampaignOpen::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(CampaignClick::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(CampaignLink::class);
    }

    public function bounces(): HasMany
    {
        return $this->hasMany(CampaignBounce::class);
    }

    public function unsubscribes(): HasMany
    {
        return $this->hasMany(CampaignUnsubscribe::class);
    }

    /**
     * Check if campaign is editable
     */
    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SCHEDULED]);
    }

    /**
     * Check if campaign can be sent
     */
    public function canBeSent(): bool
    {
        return $this->status === self::STATUS_DRAFT || $this->status === self::STATUS_SCHEDULED;
    }

    /**
     * Check if campaign can be paused
     */
    public function canBePaused(): bool
    {
        return $this->status === self::STATUS_SENDING;
    }

    /**
     * Check if campaign can be resumed
     */
    public function canBeResumed(): bool
    {
        return $this->status === self::STATUS_PAUSED;
    }

    /**
     * Calculate metrics
     */
    public function updateMetrics(): void
    {
        $this->open_rate = $this->emails_sent > 0 
            ? ($this->unique_opens / $this->emails_sent) * 100 
            : 0;
            
        $this->click_rate = $this->emails_sent > 0 
            ? ($this->unique_clicks / $this->emails_sent) * 100 
            : 0;
            
        $this->bounce_rate = $this->emails_sent > 0 
            ? ($this->emails_bounced / $this->emails_sent) * 100 
            : 0;
            
        $this->save();
    }

    /**
     * Get campaign types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_REGULAR => 'Regular Campaign',
            self::TYPE_AUTORESPONDER => 'Autoresponder',
            self::TYPE_TRIGGER => 'Triggered Campaign',
        ];
    }

    /**
     * Scope for active campaigns
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for scheduled campaigns
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('scheduled_at', '<=', now());
    }
}
