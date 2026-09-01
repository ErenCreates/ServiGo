<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Müşteri paneli ana sayfası: Kategorileri ve aktif ustaları listeler ve filtreler.
     */
    public function index(Request $request): View
    {
        $selectedCategory = $request->input('category') ?? $request->input('category_id');
        $searchQuery = $request->input('search') ?? $request->input('q');

        $categories = ServiceCategory::withCount('serviceProviders')->orderBy('name')->get();

        $query = ServiceProvider::with(['user', 'category'])
            ->withAvg(['reviews' => fn($q) => $q->where('is_approved', true)], 'rating')
            ->withCount(['reviews' => fn($q) => $q->where('is_approved', true)]);

        // Kategoriye Göre Filtreleme
        if (!empty($selectedCategory) && $selectedCategory !== 'all') {
            if (is_numeric($selectedCategory)) {
                $query->where('category_id', $selectedCategory);
            } else {
                $query->whereHas('category', function ($q) use ($selectedCategory) {
                    $q->where('name', 'like', '%' . $selectedCategory . '%');
                });
            }
        }

        // Arama Terimine Göre Filtreleme
        if (!empty($searchQuery)) {
            $term = '%' . trim($searchQuery) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('company_name', 'like', $term)
                  ->orWhere('bio', 'like', $term)
                  ->orWhere('working_hours', 'like', $term)
                  ->orWhereHas('user', function ($uq) use ($term) {
                      $uq->where('name', 'like', $term);
                  })
                  ->orWhereHas('category', function ($cq) use ($term) {
                      $cq->where('name', 'like', $term);
                  });
            });
        }

        $providers = $query->orderBy('created_at', 'desc')->get();
        $totalProvidersCount = ServiceProvider::count();

        // Müşterinin kendi taleplerini çekiyoruz
        $myRequests = \App\Models\ServiceRequest::with(['provider.user', 'category'])
            ->where('customer_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.dashboard', compact(
            'categories',
            'providers',
            'myRequests',
            'selectedCategory',
            'searchQuery',
            'totalProvidersCount'
        ));
    }

    /**
     * Seçilen ustanın detay profilini ve onaylı yorumlarını gösterir.
     */
    public function show($id): View
    {
        $provider = ServiceProvider::with([
            'user',
            'category',
            'reviews' => function ($q) {
                $q->where('is_approved', true)->with('customer')->latest();
            }
        ])->findOrFail($id);

        $averageRating = round((float) $provider->reviews->avg('rating') ?: 0, 1);
        $totalReviews = $provider->reviews->count();
        
        return view('customer.provider-detail', compact('provider', 'averageRating', 'totalReviews'));
    }

    /**
     * Hizmet talebi oluşturur.
     */
    public function storeRequest(Request $request, $providerId): RedirectResponse
    {
        $request->validate([
            'description' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $provider = ServiceProvider::findOrFail($providerId);

        ServiceRequest::create([
            'customer_id' => auth()->id(),
            'provider_id' => $provider->id,
            'category_id' => $provider->category_id,
            'description' => $request->description,
            'status'      => 'pending',
        ]);

        return redirect()
            ->route('customer.provider.show', $providerId)
            ->with('success', 'Hizmet talebiniz başarıyla ustaya iletildi.');
    }
}