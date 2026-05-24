<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Order;
use App\Models\Report;
use App\Models\Store;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users'     => User::count(),
            'total_sellers'   => User::where('role', 'seller')->count(),
            'total_buyers'    => User::where('role', 'buyer')->count(),
            'total_listings'  => Listing::count(),
            'active_listings' => Listing::available()->count(),
            'total_orders'    => Order::count(),
            'completed_orders'=> Order::where('order_status', 'completed')->count(),
            'total_revenue'   => Order::where('order_status', 'completed')->sum('total_amount'),
            'pending_stores'  => Store::where('status', 'unverified')->count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
        ];

        $recentUsers  = User::latest()->take(5)->get();
        $recentOrders = Order::with(['buyer', 'seller'])->latest()->take(5)->get();
        $pendingReports = Report::where('status', 'pending')->with('reporter')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentOrders', 'pendingReports'));
    }
}
