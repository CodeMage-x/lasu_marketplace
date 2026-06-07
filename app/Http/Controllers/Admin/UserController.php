<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::withTrashed()->with('store');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load(['store.listings', 'ordersAsBuyer', 'ordersAsSeller', 'reviewsReceived']);
        return view('admin.users.show', compact('user'));
    }

    public function suspend(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403, 'Cannot suspend an admin account.');
        // Direct property assignment — status is excluded from $fillable (VULN-16)
        $user->status = 'suspended';
        $user->save();
        return back()->with('success', "User {$user->name} has been suspended.");
    }

    public function activate(User $user): RedirectResponse
    {
        // Prevent re-activating another admin without super-admin privilege (VULN-22)
        abort_if($user->isAdmin() && !auth()->user()->isAdmin(), 403, 'Cannot change status of an admin account.');
        // Direct property assignment — status is excluded from $fillable (VULN-16)
        $user->status = 'active';
        $user->save();
        return back()->with('success', "User {$user->name} has been activated.");
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403);
        $user->delete();
        return back()->with('success', 'User soft-deleted successfully.');
    }

    public function restore(int $id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return back()->with('success', 'User restored.');
    }
}
