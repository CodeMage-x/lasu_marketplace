<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Listing::withTrashed()->with(['user', 'store', 'category']);

        if ($request->filled('q')) {
            $query->where('title', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('availability')) {
            $query->where('availability', $request->availability);
        }

        $listings = $query->latest()->paginate(20)->withQueryString();
        return view('admin.listings.index', compact('listings'));
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        $listing->delete();
        return back()->with('success', 'Listing removed.');
    }

    public function restore(int $id): RedirectResponse
    {
        Listing::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Listing restored.');
    }
}
