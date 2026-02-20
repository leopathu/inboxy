<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of users for a brand.
     */
    public function index(Brand $brand): Response
    {
        $this->authorize('viewAny', User::class);

        // Admin can see all users, single users can only see their brand's users
        if (auth()->user()->isAdmin()) {
            $users = $brand->users()->get();
        } else {
            // Check if user belongs to this brand
            if (!auth()->user()->brands->contains($brand->id)) {
                abort(403, 'You do not have access to this brand.');
            }
            $users = $brand->users()->get();
        }

        return Inertia::render('Users/Index', [
            'users' => $users,
            'brand' => $brand->only('id', 'name', 'brand_logo', 'from_name', 'from_email'),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(Brand $brand): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('Users/Create', [
            'brand' => $brand->only('id', 'name', 'brand_logo', 'from_name', 'from_email'),
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request, Brand $brand): RedirectResponse
    {
        $this->authorize('create', User::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'brand_role' => 'required|in:admin,user',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'single',
        ]);

        // Attach user to brand with role
        $brand->users()->attach($user->id, ['role' => $request->brand_role]);

        return redirect()->route('brands.users.index', $brand)->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(Brand $brand, User $user): Response
    {
        $this->authorize('update', $user);

        // Get user's role in this brand
        $brandRole = $brand->users()->where('user_id', $user->id)->first()?->pivot->role ?? 'user';

        return Inertia::render('Users/Edit', [
            'user' => array_merge($user->only('id', 'name', 'email', 'role'), ['brand_role' => $brandRole]),
            'brand' => $brand->only('id', 'name', 'brand_logo', 'from_name', 'from_email'),
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, Brand $brand, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,'.$user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'brand_role' => 'required|in:admin,user',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        // Update brand role
        $brand->users()->updateExistingPivot($user->id, ['role' => $request->brand_role]);

        return redirect()->route('brands.users.index', $brand)->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Brand $brand, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        // Detach user from brand instead of deleting
        $brand->users()->detach($user->id);

        // Only delete user if they don't belong to any other brand
        if ($user->brands()->count() === 0) {
            $user->delete();
        }

        return redirect()->route('brands.users.index', $brand)->with('success', 'User removed from brand successfully.');
    }
}
