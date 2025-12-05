<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignUnsubscribe extends Model
{
    protected $fillable = ['campaign_id', 'subscriber_id', 'ip_address', 'unsubscribed_at'];
    protected $casts = ['unsubscribed_at' => 'datetime'];

    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function subscriber(): BelongsTo { return $this->belongsTo(Subscriber::class); }
}
