<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignBounce extends Model
{
    protected $fillable = ['campaign_id', 'subscriber_id', 'type', 'reason', 'bounced_at'];
    protected $casts = ['bounced_at' => 'datetime'];

    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function subscriber(): BelongsTo { return $this->belongsTo(Subscriber::class); }
}
