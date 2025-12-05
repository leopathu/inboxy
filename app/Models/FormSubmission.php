<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_form_id',
        'subscriber_id',
        'email',
        'ip_address',
        'user_agent',
        'referrer',
        'status',
        'confirmed_at',
        'form_data',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
        'form_data' => 'array',
    ];

    public function subscriptionForm(): BelongsTo
    {
        return $this->belongsTo(SubscriptionForm::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     * Mark submission as confirmed
     */
    public function markAsConfirmed(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Mark submission as failed
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => 'failed',
        ]);
    }

    /**
     * Mark submission as spam
     */
    public function markAsSpam(): void
    {
        $this->update([
            'status' => 'spam',
        ]);
    }
}
