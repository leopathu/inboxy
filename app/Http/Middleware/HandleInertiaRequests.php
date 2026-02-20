<?php

namespace App\Http\Middleware;

use App\Models\Brand;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $brands = [];
        $selectedBrand = null;

        if ($request->user()) {
            $brands = Brand::where('user_id', $request->user()->id)
                ->orderBy('created_at', 'asc')
                ->get(['id', 'name', 'brand_logo'])
                ->map(function ($brand) {
                    return [
                        'id' => $brand->id,
                        'name' => $brand->name,
                        'logo' => $brand->brand_logo,
                    ];
                });

            // Get selected brand from session or use first brand
            $selectedBrandId = session('selected_brand_id');
            if ($selectedBrandId) {
                $selectedBrand = $brands->firstWhere('id', $selectedBrandId);
            }
            
            if (!$selectedBrand && $brands->isNotEmpty()) {
                $selectedBrand = $brands->first();
                session(['selected_brand_id' => $selectedBrand['id']]);
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                ] : null,
            ],
            'brands' => $brands,
            'selectedBrand' => $selectedBrand,
        ];
    }
}
