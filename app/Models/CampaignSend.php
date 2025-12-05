<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignSend extends Model
{
    protected $fillable = ['campaign_id', 'subscriber_id', 'status', 'sent_at', 'error_message', 'message_id'];
    protected $casts = ['sent_at' => 'datetime'];

    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function subscriber(): BelongsTo { return $this->belongsTo(Subscriber::class); }
}
