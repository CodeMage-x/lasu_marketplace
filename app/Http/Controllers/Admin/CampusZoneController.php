<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampusZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampusZoneController extends Controller
{
    public function index(): View
    {
        $zones = CampusZone::latest()->paginate(20);
        return view('admin.zones.index', compact('zones'));
    }

    public function create(): View
    {
        return view('admin.zones.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'is_active'   => ['boolean'],
        ]);

        CampusZone::create($request->only(['name', 'description', 'latitude', 'longitude']) + [
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.zones.index')->with('success', 'Campus zone created.');
    }

    public function edit(CampusZone $zone): View
    {
        return view('admin.zones.edit', compact('zone'));
    }

    public function update(Request $request, CampusZone $zone): RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],
            'is_active'   => ['boolean'],
        ]);

        $zone->update($request->only(['name', 'description', 'latitude', 'longitude']) + [
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.zones.index')->with('success', 'Campus zone updated.');
    }

    public function destroy(CampusZone $zone): RedirectResponse
    {
        $zone->delete();
        return back()->with('success', 'Zone deleted.');
    }
}
