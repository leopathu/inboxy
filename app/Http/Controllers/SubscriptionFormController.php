<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\EmailList;
use App\Models\SubscriptionForm;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionFormController extends Controller
{
    public function index(Brand $brand, EmailList $list): Response
    {
        $forms = $list->subscriptionForms()
            ->withCount('submissions')
            ->latest()
            ->paginate(15);

        return Inertia::render('SubscriptionForms/Index', [
            'brand' => $brand,
            'list' => $list,
            'forms' => $forms,
        ]);
    }

    public function create(Brand $brand, EmailList $list): Response
    {
        $customFields = $list->customFields()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'tag', 'type', 'is_required']);

        return Inertia::render('SubscriptionForms/Create', [
            'brand' => $brand,
            'list' => $list,
            'customFields' => $customFields,
        ]);
    }

    public function store(Request $request, Brand $brand, EmailList $list)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'enable_double_optin' => 'boolean',
            'send_confirmation_email' => 'boolean',
            'is_active' => 'boolean',
            'visible_fields' => 'nullable|array',
            'required_fields' => 'nullable|array',
            'success_redirect_url' => 'nullable|url',
            'failure_redirect_url' => 'nullable|url',
            'confirmation_redirect_url' => 'nullable|url',
            'success_message' => 'nullable|string',
            'already_subscribed_message' => 'nullable|string',
            'confirmation_pending_message' => 'nullable|string',
            'confirmation_email_subject' => 'nullable|string|max:255',
            'confirmation_email_body' => 'nullable|string',
            'welcome_email_subject' => 'nullable|string|max:255',
            'welcome_email_body' => 'nullable|string',
            'submit_button_text' => 'required|string|max:50',
            'primary_color' => 'nullable|string|max:7',
            'custom_css' => 'nullable|string',
            'custom_html' => 'nullable|string',
            'enable_captcha' => 'boolean',
            'captcha_type' => 'nullable|in:recaptcha,hcaptcha',
            'captcha_site_key' => 'nullable|string',
            'captcha_secret_key' => 'nullable|string',
        ]);

        $form = $list->subscriptionForms()->create($validated);

        return redirect()
            ->route('brands.lists.subscription-forms.show', [$brand->id, $list->id, $form->id])
            ->with('success', 'Subscription form created successfully.');
    }

    public function show(Brand $brand, EmailList $list, SubscriptionForm $subscriptionForm): Response
    {
        $subscriptionForm->loadCount([
            'submissions',
            'submissions as confirmed_count' => fn($q) => $q->where('status', 'confirmed'),
            'submissions as pending_count' => fn($q) => $q->where('status', 'pending'),
        ]);

        $recentSubmissions = $subscriptionForm->submissions()
            ->with('subscriber:id,name,email')
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('SubscriptionForms/Show', [
            'brand' => $brand,
            'list' => $list,
            'form' => $subscriptionForm,
            'recentSubmissions' => $recentSubmissions,
        ]);
    }

    public function edit(Brand $brand, EmailList $list, SubscriptionForm $subscriptionForm): Response
    {
        $customFields = $list->customFields()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'tag', 'type', 'is_required']);

        return Inertia::render('SubscriptionForms/Edit', [
            'brand' => $brand,
            'list' => $list,
            'form' => $subscriptionForm,
            'customFields' => $customFields,
        ]);
    }

    public function update(Request $request, Brand $brand, EmailList $list, SubscriptionForm $subscriptionForm)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'enable_double_optin' => 'boolean',
            'send_confirmation_email' => 'boolean',
            'is_active' => 'boolean',
            'visible_fields' => 'nullable|array',
            'required_fields' => 'nullable|array',
            'success_redirect_url' => 'nullable|url',
            'failure_redirect_url' => 'nullable|url',
            'confirmation_redirect_url' => 'nullable|url',
            'success_message' => 'nullable|string',
            'already_subscribed_message' => 'nullable|string',
            'confirmation_pending_message' => 'nullable|string',
            'confirmation_email_subject' => 'nullable|string|max:255',
            'confirmation_email_body' => 'nullable|string',
            'welcome_email_subject' => 'nullable|string|max:255',
            'welcome_email_body' => 'nullable|string',
            'submit_button_text' => 'required|string|max:50',
            'primary_color' => 'nullable|string|max:7',
            'custom_css' => 'nullable|string',
            'custom_html' => 'nullable|string',
            'enable_captcha' => 'boolean',
            'captcha_type' => 'nullable|in:recaptcha,hcaptcha',
            'captcha_site_key' => 'nullable|string',
            'captcha_secret_key' => 'nullable|string',
        ]);

        $subscriptionForm->update($validated);

        return redirect()
            ->route('brands.lists.subscription-forms.show', [$brand->id, $list->id, $subscriptionForm->id])
            ->with('success', 'Subscription form updated successfully.');
    }

    public function destroy(Brand $brand, EmailList $list, SubscriptionForm $subscriptionForm)
    {
        $subscriptionForm->delete();

        return redirect()
            ->route('brands.lists.subscription-forms.index', [$brand->id, $list->id])
            ->with('success', 'Subscription form deleted successfully.');
    }
}
