<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Role;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Admin Paneli ana sayfası ve genel sistem istatistikleri.
     */
    public function index(): View
    {
        $stats = [
            'total_users'        => User::count(),
            'total_customers'    => User::where('role_id', Role::CUSTOMER)->count(),
            'total_providers'    => User::where('role_id', Role::PROVIDER)->count(),
            'active_requests'    => ServiceRequest::whereIn('status', ['pending', 'accepted'])->count(),
            'completed_requests' => ServiceRequest::where('status', 'completed')->count(),
            'total_requests'     => ServiceRequest::count(),
            'total_categories'   => ServiceCategory::count(),
            'active_categories'  => ServiceCategory::where('is_active', true)->count(),
            'total_reviews'      => Review::count(),
        ];

        $recentUsers = User::with('role')->latest()->take(5)->get();
        $recentRequests = ServiceRequest::with(['customer', 'provider.user', 'category'])->latest()->take(5)->get();
        $recentReviews = Review::with(['customer', 'provider.user'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentRequests', 'recentReviews'));
    }
}
