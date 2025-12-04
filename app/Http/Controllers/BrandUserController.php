<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\User;
use App\Services\BrandService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BrandUserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly BrandService $brandService
    ) {}

    /**
     * Display a listing of users for the brand.
     */
    public function index(Brand $brand): Response
    {
        $this->authorize('view', $brand);

        $users = $brand->users()
            ->withPivot([
                'role',
                'can_manage_lists',
                'can_manage_campaigns',
                'can_manage_subscribers',
                'can_view_reports',
                'can_manage_settings',
                'can_manage_users',
            ])
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->pivot->role,
                    'permissions' => [
                        'can_manage_lists' => $user->pivot->can_manage_lists,
                        'can_manage_campaigns' => $user->pivot->can_manage_campaigns,
                        'can_manage_subscribers' => $user->pivot->can_manage_subscribers,
                        'can_view_reports' => $user->pivot->can_view_reports,
                        'can_manage_settings' => $user->pivot->can_manage_settings,
                        'can_manage_users' => $user->pivot->can_manage_users,
                    ],
                    'created_at' => $user->pivot->created_at,
                ];
            });

        return Inertia::render('Brands/Users/Index', [
            'brand' => $brand,
            'users' => $users,
        ]);
    }

    /**
     * Add a user to the brand.
     */
    public function store(Request $request, Brand $brand): RedirectResponse
    {
        $this->authorize('update', $brand);

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'create_new_user' => ['boolean'],
            'role' => ['required', Rule::in(['owner', 'manager', 'user'])],
            'can_manage_lists' => ['boolean'],
            'can_manage_campaigns' => ['boolean'],
            'can_manage_subscribers' => ['boolean'],
            'can_view_reports' => ['boolean'],
            'can_manage_settings' => ['boolean'],
            'can_manage_users' => ['boolean'],
        ]);

        // Check if user exists or create new
        if ($request->boolean('create_new_user')) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'] ?? 'password'),
                'role' => 'user',
                'is_active' => true,
            ]);
        } else {
            $user = User::where('email', $validated['email'])->firstOrFail();
        }

        // Add user to brand with permissions
        $this->brandService->addUser(
            $brand,
            $user,
            $validated['role'],
            [
                'can_manage_lists' => $validated['can_manage_lists'] ?? false,
                'can_manage_campaigns' => $validated['can_manage_campaigns'] ?? false,
                'can_manage_subscribers' => $validated['can_manage_subscribers'] ?? false,
                'can_view_reports' => $validated['can_view_reports'] ?? false,
                'can_manage_settings' => $validated['can_manage_settings'] ?? false,
                'can_manage_users' => $validated['can_manage_users'] ?? false,
            ]
        );

        return redirect()
            ->route('brands.users.index', $brand)
            ->with('success', 'User added to brand successfully.');
    }

    /**
     * Update user permissions in the brand.
     */
    public function update(Request $request, Brand $brand, User $user): RedirectResponse
    {
        $this->authorize('update', $brand);

        $validated = $request->validate([
            'role' => ['sometimes', Rule::in(['owner', 'manager', 'user'])],
            'can_manage_lists' => ['boolean'],
            'can_manage_campaigns' => ['boolean'],
            'can_manage_subscribers' => ['boolean'],
            'can_view_reports' => ['boolean'],
            'can_manage_settings' => ['boolean'],
            'can_manage_users' => ['boolean'],
        ]);

        $permissions = array_filter($validated, fn($key) => $key !== 'role', ARRAY_FILTER_USE_KEY);

        if (isset($validated['role'])) {
            $permissions['role'] = $validated['role'];
        }

        $this->brandService->updateUserPermissions($brand, $user, $permissions);

        return redirect()
            ->route('brands.users.index', $brand)
            ->with('success', 'User permissions updated successfully.');
    }

    /**
     * Remove user from the brand.
     */
    public function destroy(Brand $brand, User $user): RedirectResponse
    {
        $this->authorize('update', $brand);

        // Prevent removing the last owner
        $ownerCount = $brand->users()->wherePivot('role', 'owner')->count();
        $userRole = $brand->users()->where('users.id', $user->id)->first()?->pivot->role;

        if ($userRole === 'owner' && $ownerCount <= 1) {
            return redirect()
                ->route('brands.users.index', $brand)
                ->with('error', 'Cannot remove the last owner from the brand.');
        }

        $this->brandService->removeUser($brand, $user);

        return redirect()
            ->route('brands.users.index', $brand)
            ->with('success', 'User removed from brand successfully.');
    }
}
