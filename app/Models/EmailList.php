<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailList extends Model
{
    protected $table = 'lists';

    protected $fillable = [
        'brand_id',
        'name',
        'optin_type',
        'thank_you_enabled',
        'thank_you_subject',
        'thank_you_message',
        'confirmation_subject',
        'confirmation_message',
        'unsubscribe_enabled',
        'goodbye_subject',
        'goodbye_message',
    ];

    protected $casts = [
        'thank_you_enabled' => 'boolean',
        'unsubscribe_enabled' => 'boolean',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    // Accessor methods for counts (will be implemented when subscribers table exists)
    public function getActiveCountAttribute(): int
    {
        // TODO: Implement when subscribers table exists
        return 0;
    }

    public function getUnsubscribedCountAttribute(): int
    {
        // TODO: Implement when subscribers table exists
        return 0;
    }

    public function getBouncedCountAttribute(): int
    {
        // TODO: Implement when subscribers table exists
        return 0;
    }
}
