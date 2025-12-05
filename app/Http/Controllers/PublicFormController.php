<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SubscriptionForm;
use App\Services\FormSubmissionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicFormController extends Controller
{
    public function __construct(
        private readonly FormSubmissionService $formSubmissionService
    ) {}

    /**
     * Show the public subscription form
     */
    public function show(string $identifier): Response
    {
        $form = SubscriptionForm::where('identifier', $identifier)
            ->where('is_active', true)
            ->with(['list:id,name', 'list.customFields' => function ($query) use ($identifier) {
                $form = SubscriptionForm::where('identifier', $identifier)->first();
                if ($form && $form->visible_fields) {
                    $query->whereIn('id', $form->visible_fields);
                }
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->firstOrFail();

        return Inertia::render('Public/SubscriptionForm', [
            'form' => $form,
            'customFields' => $form->list->customFields,
        ]);
    }

    /**
     * Handle form submission
     */
    public function submit(Request $request, string $identifier)
    {
        $form = SubscriptionForm::where('identifier', $identifier)
            ->where('is_active', true)
            ->firstOrFail();

        // Build validation rules
        $rules = [
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ];

        // Add custom field validation
        if ($form->required_fields) {
            foreach ($form->required_fields as $fieldId) {
                $rules["custom_fields.{$fieldId}"] = 'required';
            }
        }

        // Add captcha validation if enabled
        if ($form->enable_captcha) {
            $rules['captcha_token'] = 'required';
            // TODO: Implement actual captcha verification
        }

        $validated = $request->validate($rules);

        try {
            $submission = $this->formSubmissionService->processSubmission(
                $form,
                $validated,
                $request->ip(),
                $request->userAgent(),
                $request->header('referer')
            );

            // Determine the message to show
            if ($submission->status === 'confirmed' && $submission->confirmed_at->lt(now()->subMinute())) {
                // Already subscribed
                $message = $form->already_subscribed_message ?: 'You are already subscribed to this list.';
                $redirectUrl = $form->success_redirect_url;
            } elseif ($submission->status === 'pending') {
                // Pending confirmation
                $message = $form->confirmation_pending_message ?: 'Please check your email to confirm your subscription.';
                $redirectUrl = null; // Stay on page to show message
            } else {
                // Successfully subscribed
                $message = $form->success_message ?: 'Thank you for subscribing!';
                $redirectUrl = $form->success_redirect_url;
            }

            if ($redirectUrl) {
                return redirect($redirectUrl)->with('success', $message);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            $message = 'An error occurred. Please try again.';
            $redirectUrl = $form->failure_redirect_url;

            if ($redirectUrl) {
                return redirect($redirectUrl)->with('error', $message);
            }

            return back()->with('error', $message)->withInput();
        }
    }

    /**
     * Confirm subscription via email link
     */
    public function confirm(string $token)
    {
        $subscriber = $this->formSubmissionService->confirmSubscription($token);

        if (!$subscriber) {
            return redirect()->route('welcome')->with('error', 'Invalid or expired confirmation link.');
        }

        // Find the form to get redirect URL
        $form = SubscriptionForm::where('list_id', $subscriber->list_id)
            ->where('is_active', true)
            ->first();

        $message = 'Your subscription has been confirmed! Thank you.';
        $redirectUrl = $form?->confirmation_redirect_url;

        if ($redirectUrl) {
            return redirect($redirectUrl)->with('success', $message);
        }

        return redirect()->route('welcome')->with('success', $message);
    }

    /**
     * Get embeddable JavaScript for the form
     */
    public function embedJs(string $identifier)
    {
        $form = SubscriptionForm::where('identifier', $identifier)
            ->where('is_active', true)
            ->firstOrFail();

        $formUrl = route('forms.show', $identifier);
        $submitUrl = route('forms.submit', $identifier);

        $js = <<<JS
(function() {
    const formContainer = document.getElementById('inboxy-form-{$identifier}');
    if (!formContainer) return;

    const formHtml = `
        <div style="max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #fff;">
            <h3 style="margin-top: 0; color: {$form->primary_color};">Subscribe to {$form->list->name}</h3>
            <form id="inboxy-subscription-form" method="POST" action="{$submitUrl}">
                <div style="margin-bottom: 15px;">
                    <input type="email" name="email" placeholder="Email address" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <input type="text" name="name" placeholder="Name (optional)"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <button type="submit" style="width: 100%; padding: 12px; background: {$form->primary_color}; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
                    {$form->submit_button_text}
                </button>
            </form>
            <div id="inboxy-form-message" style="margin-top: 15px; padding: 10px; border-radius: 4px; display: none;"></div>
        </div>
    `;

    formContainer.innerHTML = formHtml;

    const form = document.getElementById('inboxy-subscription-form');
    const messageDiv = document.getElementById('inboxy-form-message');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            messageDiv.style.display = 'block';
            if (response.ok) {
                messageDiv.style.background = '#d4edda';
                messageDiv.style.color = '#155724';
                messageDiv.textContent = data.message || 'Thank you for subscribing!';
                form.reset();
            } else {
                messageDiv.style.background = '#f8d7da';
                messageDiv.style.color = '#721c24';
                messageDiv.textContent = data.message || 'An error occurred. Please try again.';
            }
        } catch (error) {
            messageDiv.style.display = 'block';
            messageDiv.style.background = '#f8d7da';
            messageDiv.style.color = '#721c24';
            messageDiv.textContent = 'An error occurred. Please try again.';
        }
    });
})();
JS;

        return response($js)->header('Content-Type', 'application/javascript');
    }
}
