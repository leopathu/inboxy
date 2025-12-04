<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreListRequest;
use App\Http\Requests\UpdateListRequest;
use App\Models\Brand;
use App\Models\EmailList;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ListController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of email lists.
     */
    public function index(Brand $brand): Response
    {
        $this->authorize('view', $brand);

        $lists = EmailList::where('brand_id', $brand->id)
            ->withCount(['subscribers', 'activeSubscribers'])
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('Lists/Index', [
            'brand' => $brand,
            'lists' => $lists,
        ]);
    }

    /**
     * Show the form for creating a new list.
     */
    public function create(Brand $brand): Response
    {
        $this->authorize('update', $brand);

        return Inertia::render('Lists/Create', [
            'brand' => $brand,
        ]);
    }

    /**
     * Store a newly created list.
     */
    public function store(StoreListRequest $request, Brand $brand): RedirectResponse
    {
        $this->authorize('update', $brand);

        $validated = $request->validated();
        
        // Provide sensible defaults for optional fields
        $data = array_merge([
            'from_name' => $brand->name,
            'from_email' => $brand->company ? "no-reply@{$brand->company}.com" : 'no-reply@example.com',
            'require_confirmation' => true,
            'confirmation_email_subject' => 'Please confirm your subscription',
            'confirmation_email_body' => 'Please click this link to confirm: [confirmation_link]',
            'subscribe_success_message' => 'Thank you for subscribing!',
            'unsubscribe_success_message' => 'You have been unsubscribed.',
            'send_welcome_email' => false,
            'is_active' => true,
        ], $validated, [
            'brand_id' => $brand->id,
        ]);

        $list = EmailList::create($data);

        return redirect()
            ->route('brands.lists.show', [$brand, $list])
            ->with('success', 'List created successfully.');
    }

    /**
     * Display the specified list.
     */
    public function show(Brand $brand, EmailList $list): Response
    {
        $this->authorize('view', $brand);

        abort_if($list->brand_id !== $brand->id, 404);

        $list->loadCount(['subscribers', 'activeSubscribers']);
        $list->load(['customFields' => fn($query) => $query->active()->ordered()]);

        $subscribers = $list->subscribers()
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return Inertia::render('Lists/Show', [
            'brand' => $brand,
            'list' => $list,
            'subscribers' => $subscribers,
        ]);
    }

    /**
     * Show the form for editing the list.
     */
    public function edit(Brand $brand, EmailList $list): Response
    {
        $this->authorize('update', $brand);

        abort_if($list->brand_id !== $brand->id, 404);

        return Inertia::render('Lists/Edit', [
            'brand' => $brand,
            'list' => $list,
        ]);
    }

    /**
     * Update the specified list.
     */
    public function update(UpdateListRequest $request, Brand $brand, EmailList $list): RedirectResponse
    {
        $this->authorize('update', $brand);

        abort_if($list->brand_id !== $brand->id, 404);

        $list->update($request->validated());

        return redirect()
            ->route('brands.lists.show', [$brand, $list])
            ->with('success', 'List updated successfully.');
    }

    /**
     * Remove the specified list.
     */
    public function destroy(Brand $brand, EmailList $list): RedirectResponse
    {
        $this->authorize('update', $brand);

        abort_if($list->brand_id !== $brand->id, 404);

        $list->delete();

        return redirect()
            ->route('brands.lists.index', $brand)
            ->with('success', 'List deleted successfully.');
    }
}

