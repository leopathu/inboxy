<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\EmailList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListController extends Controller
{
    /**
     * Display a listing of lists for a brand.
     */
    public function index(Brand $brand): Response
    {
        // Check if user has access to this brand
        if (!auth()->user()->isAdmin() && !auth()->user()->brands->contains($brand->id)) {
            abort(403, 'You do not have access to this brand.');
        }

        $lists = $brand->lists()->get()->map(function ($list) {
            return [
                'id' => $list->id,
                'name' => $list->name,
                'optin_type' => $list->optin_type,
                'active_count' => $list->active_count,
                'unsubscribed_count' => $list->unsubscribed_count,
                'bounced_count' => $list->bounced_count,
            ];
        });

        return Inertia::render('Lists/Index', [
            'lists' => $lists,
            'brand' => $brand->only('id', 'name', 'brand_logo', 'from_name', 'from_email'),
        ]);
    }

    /**
     * Store a newly created list in storage.
     */
    public function store(Request $request, Brand $brand): RedirectResponse
    {
        // Check if user has access to this brand
        if (!auth()->user()->isAdmin() && !auth()->user()->brands->contains($brand->id)) {
            abort(403, 'You do not have access to this brand.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'optin_type' => 'required|in:single,double',
        ]);

        $brand->lists()->create($validated);

        return redirect()->route('brands.lists.index', $brand)->with('success', 'List created successfully.');
    }

    /**
     * Show the form for editing the specified list.
     */
    public function edit(Brand $brand, EmailList $list): Response
    {
        // Check if user has access to this brand
        if (!auth()->user()->isAdmin() && !auth()->user()->brands->contains($brand->id)) {
            abort(403, 'You do not have access to this brand.');
        }

        // Check if list belongs to this brand
        if ($list->brand_id !== $brand->id) {
            abort(404);
        }

        return Inertia::render('Lists/Edit', [
            'list' => $list->only([
                'id', 'name', 'optin_type',
                'thank_you_enabled', 'thank_you_subject', 'thank_you_message',
                'confirmation_subject', 'confirmation_message',
                'unsubscribe_enabled', 'goodbye_subject', 'goodbye_message'
            ]),
            'brand' => $brand->only('id', 'name', 'brand_logo', 'from_name', 'from_email'),
        ]);
    }

    /**
     * Update the specified list in storage.
     */
    public function update(Request $request, Brand $brand, EmailList $list): RedirectResponse
    {
        // Check if user has access to this brand
        if (!auth()->user()->isAdmin() && !auth()->user()->brands->contains($brand->id)) {
            abort(403, 'You do not have access to this brand.');
        }

        // Check if list belongs to this brand
        if ($list->brand_id !== $brand->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'optin_type' => 'required|in:single,double',
            'thank_you_enabled' => 'boolean',
            'thank_you_subject' => 'nullable|string|max:255',
            'thank_you_message' => 'nullable|string',
            'confirmation_subject' => 'nullable|string|max:255',
            'confirmation_message' => 'nullable|string',
            'unsubscribe_enabled' => 'boolean',
            'goodbye_subject' => 'nullable|string|max:255',
            'goodbye_message' => 'nullable|string',
        ]);

        $list->update($validated);

        return redirect()->route('brands.lists.index', $brand)->with('success', 'List updated successfully.');
    }

    /**
     * Remove the specified list from storage.
     */
    public function destroy(Brand $brand, EmailList $list): RedirectResponse
    {
        // Check if user has access to this brand
        if (!auth()->user()->isAdmin() && !auth()->user()->brands->contains($brand->id)) {
            abort(403, 'You do not have access to this brand.');
        }

        // Check if list belongs to this brand
        if ($list->brand_id !== $brand->id) {
            abort(404);
        }

        $list->delete();

        return redirect()->route('brands.lists.index', $brand)->with('success', 'List deleted successfully.');
    }
}
