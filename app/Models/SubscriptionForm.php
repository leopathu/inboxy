<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SubscriptionForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'list_id',
        'name',
        'identifier',
        'description',
        'enable_double_optin',
        'send_confirmation_email',
        'is_active',
        'visible_fields',
        'required_fields',
        'success_redirect_url',
        'failure_redirect_url',
        'confirmation_redirect_url',
        'success_message',
        'already_subscribed_message',
        'confirmation_pending_message',
        'confirmation_email_subject',
        'confirmation_email_body',
        'welcome_email_subject',
        'welcome_email_body',
        'submit_button_text',
        'primary_color',
        'custom_css',
        'custom_html',
        'enable_captcha',
        'captcha_type',
        'captcha_site_key',
        'captcha_secret_key',
    ];

    protected $casts = [
        'enable_double_optin' => 'boolean',
        'send_confirmation_email' => 'boolean',
        'is_active' => 'boolean',
        'visible_fields' => 'array',
        'required_fields' => 'array',
        'enable_captcha' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($form) {
            if (empty($form->identifier)) {
                $form->identifier = Str::slug($form->name) . '-' . Str::random(8);
            }
        });
    }

    public function list(): BelongsTo
    {
        return $this->belongsTo(EmailList::class, 'list_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    /**
     * Get the public URL for this form
     */
    public function getPublicUrlAttribute(): string
    {
        return route('forms.show', $this->identifier);
    }

    /**
     * Get embeddable HTML code
     */
    public function getEmbedHtmlCode(): string
    {
        $url = $this->public_url;
        return <<<HTML
<div id="inboxy-form-{$this->identifier}"></div>
<script src="{$url}/embed.js"></script>
HTML;
    }

    /**
     * Get embeddable JavaScript code
     */
    public function getEmbedJsCode(): string
    {
        $url = $this->public_url;
        return <<<JS
(function() {
    var script = document.createElement('script');
    script.src = '{$url}/embed.js';
    script.async = true;
    document.body.appendChild(script);
})();
JS;
    }

    /**
     * Get default confirmation email body
     */
    public function getDefaultConfirmationEmail(): array
    {
        return [
            'subject' => 'Please confirm your subscription',
            'body' => "Hello!\n\nPlease click the link below to confirm your subscription:\n\n{{confirmation_link}}\n\nIf you didn't request this, please ignore this email.\n\nThank you!",
        ];
    }

    /**
     * Get default welcome email body
     */
    public function getDefaultWelcomeEmail(): array
    {
        return [
            'subject' => 'Welcome! Your subscription is confirmed',
            'body' => "Hello {{name}}!\n\nThank you for subscribing to {$this->list->name}.\n\nYou'll receive updates from us at {{email}}.\n\nThank you!",
        ];
    }
}
