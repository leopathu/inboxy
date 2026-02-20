<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandDashboardController extends Controller
{
    public function index(Brand $brand)
    {
        // Check if user has access to this brand
        if (!auth()->user()->isAdmin() && !auth()->user()->brands->contains($brand->id)) {
            abort(403, 'You do not have access to this brand.');
        }

        return Inertia::render('Brands/Dashboard', [
            'brand' => $brand->only('id', 'name', 'brand_logo', 'from_name', 'from_email'),
            'stats' => [
                'total_users' => $brand->users()->count(),
                // Add more stats as needed
            ],
        ]);
    }
}

