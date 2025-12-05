<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignClick extends Model
{
    protected $fillable = ['campaign_id', 'subscriber_id', 'link_id', 'ip_address', 'user_agent', 'country', 'city', 'clicked_at'];
    protected $casts = ['clicked_at' => 'datetime'];

    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function subscriber(): BelongsTo { return $this->belongsTo(Subscriber::class); }
    public function link(): BelongsTo { return $this->belongsTo(CampaignLink::class); }
}
