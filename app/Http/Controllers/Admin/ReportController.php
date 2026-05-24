<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $query = Report::with(['reporter', 'reportable', 'reviewedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->latest()->paginate(20)->withQueryString();
        return view('admin.reports.index', compact('reports'));
    }

    public function review(Report $report): RedirectResponse
    {
        $report->update([
            'status'      => 'reviewed',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        return back()->with('success', 'Report marked as reviewed.');
    }

    public function resolve(Report $report): RedirectResponse
    {
        $report->update([
            'status'      => 'resolved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        return back()->with('success', 'Report resolved.');
    }
}
