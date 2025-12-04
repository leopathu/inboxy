<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\Brand;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ApiKeyController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of API keys for the brand.
     */
    public function index(Brand $brand): Response
    {
        $this->authorize('view', $brand);

        $apiKeys = $brand->apiKeys()
            ->with('user:id,name,email')
            ->latest()
            ->get()
            ->map(function ($apiKey) {
                return [
                    'id' => $apiKey->id,
                    'name' => $apiKey->name,
                    'key' => $apiKey->key,
                    'permissions' => $apiKey->permissions,
                    'allowed_ips' => $apiKey->allowed_ips,
                    'requests_count' => $apiKey->requests_count,
                    'daily_limit' => $apiKey->daily_limit,
                    'last_used_at' => $apiKey->last_used_at,
                    'is_active' => $apiKey->is_active,
                    'expires_at' => $apiKey->expires_at,
                    'created_by' => $apiKey->user ? [
                        'name' => $apiKey->user->name,
                        'email' => $apiKey->user->email,
                    ] : null,
                    'created_at' => $apiKey->created_at,
                ];
            });

        return Inertia::render('Brands/ApiKeys/Index', [
            'brand' => $brand,
            'apiKeys' => $apiKeys,
        ]);
    }

    /**
     * Show the form for creating a new API key.
     */
    public function create(Brand $brand): Response
    {
        $this->authorize('update', $brand);

        return Inertia::render('Brands/ApiKeys/Create', [
            'brand' => $brand,
        ]);
    }

    /**
     * Store a newly created API key.
     */
    public function store(Request $request, Brand $brand): RedirectResponse
    {
        $this->authorize('update', $brand);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
            'allowed_ips' => ['nullable', 'array'],
            'allowed_ips.*' => ['ip'],
            'daily_limit' => ['nullable', 'integer', 'min:0'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        $apiKey = ApiKey::generate(
            $brand,
            Auth::user(),
            $validated['name']
        );

        // Update optional fields
        $apiKey->update([
            'permissions' => $validated['permissions'] ?? [],
            'allowed_ips' => $validated['allowed_ips'] ?? [],
            'daily_limit' => $validated['daily_limit'] ?? 0,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return redirect()
            ->route('brands.api-keys.index', $brand)
            ->with('success', 'API key created successfully.')
            ->with('api_key_secret', $apiKey->secret); // Show secret only once
    }

    /**
     * Show the form for editing the API key.
     */
    public function edit(Brand $brand, ApiKey $apiKey): Response
    {
        $this->authorize('update', $brand);

        if ($apiKey->brand_id !== $brand->id) {
            abort(404);
        }

        return Inertia::render('Brands/ApiKeys/Edit', [
            'brand' => $brand,
            'apiKey' => [
                'id' => $apiKey->id,
                'name' => $apiKey->name,
                'key' => $apiKey->key,
                'permissions' => $apiKey->permissions,
                'allowed_ips' => $apiKey->allowed_ips,
                'daily_limit' => $apiKey->daily_limit,
                'is_active' => $apiKey->is_active,
                'expires_at' => $apiKey->expires_at,
            ],
        ]);
    }

    /**
     * Update the specified API key.
     */
    public function update(Request $request, Brand $brand, ApiKey $apiKey): RedirectResponse
    {
        $this->authorize('update', $brand);

        if ($apiKey->brand_id !== $brand->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
            'allowed_ips' => ['nullable', 'array'],
            'allowed_ips.*' => ['ip'],
            'daily_limit' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        $apiKey->update($validated);

        return redirect()
            ->route('brands.api-keys.index', $brand)
            ->with('success', 'API key updated successfully.');
    }

    /**
     * Remove the specified API key.
     */
    public function destroy(Brand $brand, ApiKey $apiKey): RedirectResponse
    {
        $this->authorize('update', $brand);

        if ($apiKey->brand_id !== $brand->id) {
            abort(404);
        }

        $apiKey->delete();

        return redirect()
            ->route('brands.api-keys.index', $brand)
            ->with('success', 'API key deleted successfully.');
    }

    /**
     * Regenerate the API key (new key and secret).
     */
    public function regenerate(Brand $brand, ApiKey $apiKey): RedirectResponse
    {
        $this->authorize('update', $brand);

        if ($apiKey->brand_id !== $brand->id) {
            abort(404);
        }

        // Store current settings
        $name = $apiKey->name;
        $permissions = $apiKey->permissions;
        $allowedIps = $apiKey->allowed_ips;
        $dailyLimit = $apiKey->daily_limit;
        $expiresAt = $apiKey->expires_at;

        // Delete old key
        $apiKey->delete();

        // Generate new key with same settings
        $newApiKey = ApiKey::generate($brand, Auth::user(), $name);
        $newApiKey->update([
            'permissions' => $permissions,
            'allowed_ips' => $allowedIps,
            'daily_limit' => $dailyLimit,
            'expires_at' => $expiresAt,
        ]);

        return redirect()
            ->route('brands.api-keys.index', $brand)
            ->with('success', 'API key regenerated successfully.')
            ->with('api_key_secret', $newApiKey->secret); // Show new secret
    }
}
