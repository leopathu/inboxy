<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Brand;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBrandAccess
{
    /**
     * Handle an incoming request.
     *
     * Verify user has access to the brand specified in route parameter.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $permission  Optional permission to check
     */
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get brand from route parameter (model binding) or current brand
        $brand = $request->route('brand');
        
        if ($brand instanceof Brand) {
            // Brand was resolved via route model binding
            $brandModel = $brand;
        } elseif ($brand) {
            // Brand is an ID, fetch it
            $brandModel = Brand::find($brand);
        } else {
            // No brand in route, try current brand
            if (!$user->current_brand_id) {
                return redirect()->route('brands.user')
                    ->with('error', 'No brand selected. Please select a brand.');
            }
            $brandModel = Brand::find($user->current_brand_id);
        }
        
        if (!$brandModel) {
            abort(404, 'Brand not found.');
        }

        // Check if user has access to this brand
        if (!$user->hasAccessTo($brandModel)) {
            abort(403, 'You do not have access to this brand.');
        }

        // Check specific permission if provided
        if ($permission && !$user->canInBrand($brandModel, $permission)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        // Share brand with all views
        $request->merge(['current_brand' => $brandModel]);
        
        return $next($request);
    }
}
