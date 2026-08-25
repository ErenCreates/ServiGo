<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Müşteri paneli ana sayfası: Kategorileri ve aktif ustaları listeler.
     */
    public function index(): View
    {
        $categories = ServiceCategory::orderBy('name')->get();
        // user ve category ilişkileriyle beraber tüm ustaları getiriyoruz
        $providers = ServiceProvider::with(['user', 'category'])->get();

        return view('customer.dashboard', compact('categories', 'providers'));
    }

    /**
     * Seçilen ustanın detay profilini gösterir.
     */
    public function show($id): View
    {
        $provider = ServiceProvider::with(['user', 'category'])->findOrFail($id);
        
        return view('customer.provider-detail', compact('provider'));
    }
}