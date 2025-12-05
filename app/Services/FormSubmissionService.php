<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FormSubmission;
use App\Models\Subscriber;
use App\Models\SubscriptionForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class FormSubmissionService
{
    /**
     * Process a form submission
     */
    public function processSubmission(
        SubscriptionForm $form,
        array $data,
        string $ipAddress,
        ?string $userAgent = null,
        ?string $referrer = null
    ): FormSubmission {
        return DB::transaction(function () use ($form, $data, $ipAddress, $userAgent, $referrer) {
            // Check if subscriber already exists
            $subscriber = Subscriber::where('list_id', $form->list_id)
                ->where('email', $data['email'])
                ->first();

            if ($subscriber && $subscriber->status === 'subscribed') {
                // Already subscribed - create submission record
                $submission = $form->submissions()->create([
                    'subscriber_id' => $subscriber->id,
                    'email' => $data['email'],
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'referrer' => $referrer,
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                    'form_data' => $data,
                ]);

                return $submission;
            }

            // Create or update subscriber
            if (!$subscriber) {
                $subscriber = Subscriber::create([
                    'list_id' => $form->list_id,
                    'email' => $data['email'],
                    'name' => $data['name'] ?? '',
                    'status' => $form->enable_double_optin ? 'unconfirmed' : 'subscribed',
                    'subscribed_at' => $form->enable_double_optin ? null : now(),
                    'confirmation_token' => Str::random(64),
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'source' => 'form',
                ]);
            } else {
                // Update existing unconfirmed/unsubscribed subscriber
                $subscriber->update([
                    'name' => $data['name'] ?? $subscriber->name,
                    'status' => $form->enable_double_optin ? 'unconfirmed' : 'subscribed',
                    'subscribed_at' => $form->enable_double_optin ? null : now(),
                    'confirmation_token' => Str::random(64),
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                ]);
            }

            // Store custom field values
            if (isset($data['custom_fields']) && is_array($data['custom_fields'])) {
                foreach ($data['custom_fields'] as $fieldId => $value) {
                    $subscriber->customFieldValues()->updateOrCreate(
                        ['custom_field_id' => $fieldId],
                        ['value' => $value]
                    );
                }
            }

            // Create form submission record
            $submission = $form->submissions()->create([
                'subscriber_id' => $subscriber->id,
                'email' => $data['email'],
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'referrer' => $referrer,
                'status' => $form->enable_double_optin ? 'pending' : 'confirmed',
                'confirmed_at' => $form->enable_double_optin ? null : now(),
                'form_data' => $data,
            ]);

            // Send confirmation email if double opt-in is enabled
            if ($form->enable_double_optin && $form->send_confirmation_email) {
                $this->sendConfirmationEmail($form, $subscriber);
            } elseif (!$form->enable_double_optin && $form->send_confirmation_email) {
                // Send welcome email for single opt-in
                $this->sendWelcomeEmail($form, $subscriber);
            }

            // Update list subscriber count
            $form->list->updateSubscriberCounts();

            return $submission;
        });
    }

    /**
     * Confirm a subscription via token
     */
    public function confirmSubscription(string $token): ?Subscriber
    {
        $subscriber = Subscriber::where('confirmation_token', $token)
            ->where('status', 'unconfirmed')
            ->first();

        if (!$subscriber) {
            return null;
        }

        $subscriber->update([
            'status' => 'subscribed',
            'subscribed_at' => now(),
            'confirmation_token' => null,
        ]);

        // Update form submission status
        FormSubmission::where('subscriber_id', $subscriber->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

        // Send welcome email if configured
        $form = SubscriptionForm::where('list_id', $subscriber->list_id)
            ->where('is_active', true)
            ->first();

        if ($form && $form->send_confirmation_email && $form->welcome_email_body) {
            $this->sendWelcomeEmail($form, $subscriber);
        }

        // Update list subscriber count
        $subscriber->list->updateSubscriberCounts();

        return $subscriber;
    }

    /**
     * Send confirmation email with double opt-in link
     */
    protected function sendConfirmationEmail(SubscriptionForm $form, Subscriber $subscriber): void
    {
        $confirmationUrl = route('forms.confirm', $subscriber->confirmation_token);
        
        $subject = $form->confirmation_email_subject ?: $form->getDefaultConfirmationEmail()['subject'];
        $body = $form->confirmation_email_body ?: $form->getDefaultConfirmationEmail()['body'];

        // Replace tags
        $body = $this->replaceTags($body, $subscriber, [
            'confirmation_link' => $confirmationUrl,
        ]);

        // Send email (simplified - in production use Mail facade with proper template)
        // TODO: Implement proper email sending via SES
        Mail::raw($body, function ($message) use ($subscriber, $subject, $form) {
            $message->to($subscriber->email, $subscriber->name)
                ->subject($subject)
                ->from($form->list->from_email, $form->list->from_name);
        });
    }

    /**
     * Send welcome email after confirmation
     */
    protected function sendWelcomeEmail(SubscriptionForm $form, Subscriber $subscriber): void
    {
        if (!$form->welcome_email_body) {
            return;
        }

        $subject = $form->welcome_email_subject ?: $form->getDefaultWelcomeEmail()['subject'];
        $body = $form->welcome_email_body ?: $form->getDefaultWelcomeEmail()['body'];

        // Replace tags
        $body = $this->replaceTags($body, $subscriber);

        // Send email
        Mail::raw($body, function ($message) use ($subscriber, $subject, $form) {
            $message->to($subscriber->email, $subscriber->name)
                ->subject($subject)
                ->from($form->list->from_email, $form->list->from_name);
        });
    }

    /**
     * Replace personalization tags in content
     */
    protected function replaceTags(string $content, Subscriber $subscriber, array $extraTags = []): string
    {
        $tags = array_merge([
            'name' => $subscriber->name,
            'email' => $subscriber->email,
            'first_name' => explode(' ', $subscriber->name)[0] ?? '',
            'last_name' => implode(' ', array_slice(explode(' ', $subscriber->name), 1)) ?: '',
        ], $extraTags);

        foreach ($tags as $tag => $value) {
            $content = str_replace("{{" . $tag . "}}", $value, $content);
        }

        return $content;
    }
}
