<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignLink extends Model
{
    protected $fillable = ['campaign_id', 'url', 'hash', 'click_count', 'unique_clicks'];

    public function campaign(): BelongsTo { return $this->belongsTo(Campaign::class); }
    public function clicks(): HasMany { return $this->hasMany(CampaignClick::class, 'link_id'); }
}
