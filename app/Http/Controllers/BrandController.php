<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Brand::class);

        $brands = Brand::with('user')->latest()->paginate(10);

        return Inertia::render('Brands/Index', [
            'brands' => $brands,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Brand::class);

        return Inertia::render('Brands/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Brand::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email|max:255',
            'reply_to_email' => 'required|email|max:255',
            'brand_logo' => 'nullable|string',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|string|in:tls,ssl,none',
            'smtp_from_address' => 'nullable|email|max:255',
            'smtp_from_name' => 'nullable|string|max:255',
            'recaptcha_site_key' => 'nullable|string|max:255',
            'recaptcha_secret_key' => 'nullable|string|max:255',
            'show_gdpr_options' => 'boolean',
            'only_campaigns_with_gdpr' => 'boolean',
            'only_autoresponders_with_gdpr' => 'boolean',
            'track_opens' => 'boolean',
            'track_clicks' => 'boolean',
            'default_url_query_string' => 'nullable|string|max:255',
            'test_email_subject_prefix' => 'nullable|string|max:255',
            'allowed_attachment_types' => 'nullable|array',
            'default_optin_method' => 'required|string|in:single,double',
        ]);

        $validated['user_id'] = auth()->id();

        Brand::create($validated);

        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        $this->authorize('update', $brand);

        return Inertia::render('Brands/Edit', [
            'brand' => $brand,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $this->authorize('update', $brand);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email|max:255',
            'reply_to_email' => 'required|email|max:255',
            'brand_logo' => 'nullable|string',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|string|in:tls,ssl,none',
            'smtp_from_address' => 'nullable|email|max:255',
            'smtp_from_name' => 'nullable|string|max:255',
            'recaptcha_site_key' => 'nullable|string|max:255',
            'recaptcha_secret_key' => 'nullable|string|max:255',
            'show_gdpr_options' => 'boolean',
            'only_campaigns_with_gdpr' => 'boolean',
            'only_autoresponders_with_gdpr' => 'boolean',
            'track_opens' => 'boolean',
            'track_clicks' => 'boolean',
            'default_url_query_string' => 'nullable|string|max:255',
            'test_email_subject_prefix' => 'nullable|string|max:255',
            'allowed_attachment_types' => 'nullable|array',
            'default_optin_method' => 'required|string|in:single,double',
        ]);

        $brand->update($validated);

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $this->authorize('delete', $brand);

        $brand->delete();

        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully.');
    }
}
