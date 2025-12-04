<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    public function __construct(
        private readonly BrandService $brandService
    ) {}

    /**
     * Display a listing of brands (Admin only).
     */
    public function index(): Response
    {
        return Inertia::render('Brands/Index', [
            'brands' => $this->brandService->getPaginated(),
        ]);
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create(): Response
    {
        return Inertia::render('Brands/Create');
    }

    /**
     * Store a newly created brand.
     */
    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $brand = $this->brandService->create(
            $request->validated(),
            $request->user()
        );

        return redirect()->route('brands.show', $brand)
            ->with('success', 'Brand created successfully.');
    }

    /**
     * Display the specified brand.
     */
    public function show(Brand $brand): Response
    {
        $brand->load(['users' => function ($query) {
            $query->withPivot('role');
        }]);

        return Inertia::render('Brands/Show', [
            'brand' => $brand,
            'statistics' => $this->brandService->getStatistics($brand),
        ]);
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand): Response
    {
        return Inertia::render('Brands/Edit', [
            'brand' => $brand,
        ]);
    }

    /**
     * Update the specified brand.
     */
    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $this->brandService->update($brand, $request->validated());

        return redirect()->route('brands.show', $brand)
            ->with('success', 'Brand updated successfully.');
    }

    /**
     * Remove the specified brand.
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        $this->brandService->delete($brand);

        return redirect()->route('brands.index')
            ->with('success', 'Brand deleted successfully.');
    }

    /**
     * Switch to a different brand.
     */
    public function switch(Brand $brand): RedirectResponse
    {
        $user = request()->user();

        if (!$user->hasAccessTo($brand)) {
            return redirect()->back()
                ->with('error', 'You do not have access to this brand.');
        }

        $user->switchToBrand($brand);

        return redirect()->route('dashboard')
            ->with('success', "Switched to {$brand->name}.");
    }

    /**
     * Get user's accessible brands.
     */
    public function userBrands(): Response
    {
        $user = request()->user();

        return Inertia::render('Brands/UserBrands', [
            'brands' => $this->brandService->getUserBrands($user),
        ]);
    }
}
