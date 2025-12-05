<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Campaign;
use App\Models\EmailList;
use App\Models\EmailTemplate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CampaignController extends Controller
{
    use AuthorizesRequests;
    public function index(): Response
    {
        $brand = auth()->user()->currentBrand;
        
        $campaigns = Campaign::with(['user', 'list'])
            ->where('brand_id', $brand->id)
            ->latest()
            ->paginate(20);

        return Inertia::render('Campaigns/Index', [
            'brand' => $brand,
            'campaigns' => $campaigns,
        ]);
    }

    public function create(): Response
    {
        $brand = auth()->user()->currentBrand;
        
        $lists = EmailList::where('brand_id', $brand->id)
            ->orderBy('name')
            ->get(['id', 'name', 'subscriber_count']);

        $templates = EmailTemplate::where('brand_id', $brand->id)
            ->orWhere('is_public', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'thumbnail']);

        return Inertia::render('Campaigns/Create', [
            'brand' => $brand,
            'lists' => $lists,
            'templates' => $templates,
            'types' => Campaign::getTypes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email|max:255',
            'reply_to_email' => 'nullable|email|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Campaign::getTypes())),
            'list_id' => 'required|exists:email_lists,id',
            'template_id' => 'nullable|exists:email_templates,id',
            'html_content' => 'required|string',
            'plain_text_content' => 'nullable|string',
            'send_to' => 'required|in:all,subscribed,unconfirmed',
            'segment' => 'nullable|json',
            'track_opens' => 'boolean',
            'track_clicks' => 'boolean',
            'google_analytics' => 'nullable|string|max:255',
            'scheduled_at' => 'nullable|date|after:now',
            'throttle_rate' => 'nullable|integer|min:0',
            'delay_value' => 'nullable|integer|min:0',
            'delay_unit' => 'nullable|in:minutes,hours,days',
            'trigger_event' => 'nullable|string|max:100',
            'attachments' => 'nullable|json',
        ]);

        $campaign = Campaign::create([
            ...$validated,
            'brand_id' => auth()->user()->currentBrand->id,
            'user_id' => auth()->id(),
            'status' => $validated['scheduled_at'] ? Campaign::STATUS_SCHEDULED : Campaign::STATUS_DRAFT,
        ]);

        return redirect()->route('brands.campaigns.show', [$campaign->brand_id, $campaign])
            ->with('success', 'Campaign created successfully.');
    }

    public function show(Brand $brand, Campaign $campaign): Response
    {
        $this->authorize('view', $campaign);

        $campaign->load(['user', 'list', 'template']);

        $stats = [
            'total_sends' => $campaign->sends()->count(),
            'delivered' => $campaign->sends()->where('status', 'sent')->count(),
            'failed' => $campaign->sends()->where('status', 'failed')->count(),
            'opens' => $campaign->opens()->count(),
            'unique_opens' => $campaign->opens()->distinct('subscriber_id')->count('subscriber_id'),
            'clicks' => $campaign->clicks()->count(),
            'unique_clicks' => $campaign->clicks()->distinct('subscriber_id')->count('subscriber_id'),
            'bounces' => $campaign->bounces()->count(),
            'complaints' => $campaign->bounces()->where('type', 'complaint')->count(),
            'unsubscribes' => $campaign->unsubscribes()->count(),
        ];

        return Inertia::render('Campaigns/Show', [
            'brand' => $brand,
            'campaign' => $campaign,
            'stats' => $stats,
        ]);
    }

    public function edit(Brand $brand, Campaign $campaign): Response|RedirectResponse
    {
        $this->authorize('update', $campaign);

        if (!$campaign->isEditable()) {
            return redirect()->route('brands.campaigns.show', [$campaign->brand_id, $campaign])
                ->with('error', 'This campaign cannot be edited.');
        }

        $lists = EmailList::where('brand_id', auth()->user()->currentBrand->id)
            ->orderBy('name')
            ->get(['id', 'name', 'subscriber_count']);

        $templates = EmailTemplate::where('brand_id', auth()->user()->currentBrand->id)
            ->orWhere('is_public', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'thumbnail']);

        return Inertia::render('Campaigns/Edit', [
            'campaign' => $campaign,
            'lists' => $lists,
            'templates' => $templates,
            'types' => Campaign::getTypes(),
        ]);
    }

    public function update(Request $request, Brand $brand, Campaign $campaign): RedirectResponse
    {
        $this->authorize('update', $campaign);

        if (!$campaign->isEditable()) {
            return redirect()->route('brands.campaigns.show', [$campaign->brand_id, $campaign])
                ->with('error', 'This campaign cannot be edited.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email|max:255',
            'reply_to_email' => 'nullable|email|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Campaign::getTypes())),
            'list_id' => 'required|exists:email_lists,id',
            'template_id' => 'nullable|exists:email_templates,id',
            'html_content' => 'required|string',
            'plain_text_content' => 'nullable|string',
            'send_to' => 'required|in:all,subscribed,unconfirmed',
            'segment' => 'nullable|json',
            'track_opens' => 'boolean',
            'track_clicks' => 'boolean',
            'google_analytics' => 'nullable|string|max:255',
            'scheduled_at' => 'nullable|date|after:now',
            'throttle_rate' => 'nullable|integer|min:0',
            'delay_value' => 'nullable|integer|min:0',
            'delay_unit' => 'nullable|in:minutes,hours,days',
            'trigger_event' => 'nullable|string|max:100',
            'attachments' => 'nullable|json',
        ]);

        $campaign->update([
            ...$validated,
            'status' => $validated['scheduled_at'] ? Campaign::STATUS_SCHEDULED : Campaign::STATUS_DRAFT,
        ]);

        return redirect()->route('brands.campaigns.show', [$campaign->brand_id, $campaign])
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Brand $brand, Campaign $campaign): RedirectResponse
    {
        $this->authorize('delete', $campaign);

        if (!$campaign->isEditable()) {
            return redirect()->route('brands.campaigns.show', [$campaign->brand_id, $campaign])
                ->with('error', 'This campaign cannot be deleted.');
        }

        $campaign->delete();

        return redirect()->route('brands.campaigns.index', $campaign->brand_id)
            ->with('success', 'Campaign deleted successfully.');
    }

    public function duplicate(Brand $brand, Campaign $campaign): RedirectResponse
    {
        $this->authorize('view', $campaign);

        $newCampaign = $campaign->replicate([
            'status',
            'scheduled_at',
            'sent_at',
            'completed_at',
            'total_recipients',
            'total_sent',
            'total_delivered',
            'total_opens',
            'total_clicks',
            'total_bounces',
            'total_complaints',
            'total_unsubscribes',
        ]);

        $newCampaign->name = $campaign->name . ' (Copy)';
        $newCampaign->status = Campaign::STATUS_DRAFT;
        $newCampaign->user_id = auth()->id();
        $newCampaign->save();

        return redirect()->route('brands.campaigns.edit', [$newCampaign->brand_id, $newCampaign])
            ->with('success', 'Campaign duplicated successfully.');
    }

    public function pause(Brand $brand, Campaign $campaign): RedirectResponse
    {
        $this->authorize('update', $campaign);

        if (!$campaign->canBePaused()) {
            return back()->with('error', 'This campaign cannot be paused.');
        }

        $campaign->update(['status' => Campaign::STATUS_PAUSED]);

        return back()->with('success', 'Campaign paused successfully.');
    }

    public function resume(Brand $brand, Campaign $campaign): RedirectResponse
    {
        $this->authorize('update', $campaign);

        if (!$campaign->canBeResumed()) {
            return back()->with('error', 'This campaign cannot be resumed.');
        }

        $campaign->update(['status' => Campaign::STATUS_SENDING]);

        return back()->with('success', 'Campaign resumed successfully.');
    }

    public function cancel(Brand $brand, Campaign $campaign): RedirectResponse
    {
        $this->authorize('update', $campaign);

        if (!in_array($campaign->status, [Campaign::STATUS_SCHEDULED, Campaign::STATUS_SENDING, Campaign::STATUS_PAUSED])) {
            return back()->with('error', 'This campaign cannot be cancelled.');
        }

        $campaign->update(['status' => Campaign::STATUS_CANCELLED]);

        return back()->with('success', 'Campaign cancelled successfully.');
    }
}
