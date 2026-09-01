<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Tüm müşteri yorum ve değerlendirmelerinin genel görünümü.
     */
    public function index(Request $request): View
    {
        $query = Review::with(['customer', 'provider.user', 'provider.category', 'serviceRequest']);

        // Onay Durumu Filtresi
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        // Puan Filtresi
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Arama (Yorum içeriği, Müşteri adı, Usta adı)
        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', $search)
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', $search);
                  })
                  ->orWhereHas('provider.user', function ($puq) use ($search) {
                      $puq->where('name', 'like', $search);
                  });
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total'    => Review::count(),
            'approved' => Review::where('is_approved', true)->count(),
            'pending'  => Review::where('is_approved', false)->count(),
            'avg'      => round((float) Review::avg('rating') ?: 0, 1),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    /**
     * Yorumun onay / yayınlanma durumunu değiştirir.
     */
    public function toggleApproval($id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->is_approved = !$review->is_approved;
        $review->save();

        $statusText = $review->is_approved ? 'onaylandı ve yayına alındı' : 'yayından kaldırıldı';
        return redirect()
            ->back()
            ->with('success', "Yorum (#{$review->id}) başarıyla {$statusText}.");
    }

    /**
     * Yorumu siler.
     */
    public function destroy($id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Yorum sistemden silindi.');
    }
}

