<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function index(Request $request): View
    {
        $query = Store::withTrashed()->with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        $stores = $query->latest()->paginate(20)->withQueryString();
        return view('admin.stores.index', compact('stores'));
    }

    public function verify(Store $store): RedirectResponse
    {
        $store->update(['status' => 'verified']);
        return back()->with('success', "Store \"{$store->name}\" has been verified.");
    }

    public function suspend(Store $store): RedirectResponse
    {
        $store->update(['status' => 'suspended']);
        return back()->with('success', "Store \"{$store->name}\" has been suspended.");
    }
}
