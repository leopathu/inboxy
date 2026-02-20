<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'from_name',
        'from_email',
        'reply_to_email',
        'brand_logo',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
        'recaptcha_site_key',
        'recaptcha_secret_key',
        'show_gdpr_options',
        'only_campaigns_with_gdpr',
        'only_autoresponders_with_gdpr',
        'track_opens',
        'track_clicks',
        'default_url_query_string',
        'test_email_subject_prefix',
        'allowed_attachment_types',
        'default_optin_method',
    ];

    protected $casts = [
        'smtp_port' => 'integer',
        'show_gdpr_options' => 'boolean',
        'only_campaigns_with_gdpr' => 'boolean',
        'only_autoresponders_with_gdpr' => 'boolean',
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
        'allowed_attachment_types' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function lists(): HasMany
    {
        return $this->hasMany(EmailList::class);
    }
}
