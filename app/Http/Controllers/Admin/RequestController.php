<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RequestController extends Controller
{
    /**
     * Tüm hizmet talepleri ve randevuların genel görünümü.
     */
    public function index(Request $request): View
    {
        $query = ServiceRequest::with(['customer', 'provider.user', 'category']);

        // Durum Filtresi
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Kategori Filtresi
        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->where('category_id', $request->category_id);
        }

        // Arama (Açıklama, Müşteri Adı, Usta Adı, Şirket Adı)
        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', $search)
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', $search)->orWhere('email', 'like', $search);
                  })
                  ->orWhereHas('provider.user', function ($puq) use ($search) {
                      $puq->where('name', 'like', $search);
                  })
                  ->orWhereHas('provider', function ($pq) use ($search) {
                      $pq->where('company_name', 'like', $search);
                  });
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $categories = ServiceCategory::orderBy('name')->get();

        $statusCounts = [
            'all'       => ServiceRequest::count(),
            'pending'   => ServiceRequest::where('status', 'pending')->count(),
            'accepted'  => ServiceRequest::where('status', 'accepted')->count(),
            'completed' => ServiceRequest::where('status', 'completed')->count(),
            'rejected'  => ServiceRequest::where('status', 'rejected')->count(),
        ];

        return view('admin.requests.index', compact('requests', 'categories', 'statusCounts'));
    }

    /**
     * Talep veya randevu durumunu yönetici olarak günceller.
     */
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,accepted,rejected,completed'],
        ]);

        $serviceRequest = ServiceRequest::findOrFail($id);
        $serviceRequest->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->back()
            ->with('success', "Talep (#{$serviceRequest->id}) durumu başarıyla '{$validated['status']}' olarak güncellendi.");
    }

    /**
     * Talebi veya randevuyu sistemden siler.
     */
    public function destroy($id): RedirectResponse
    {
        $serviceRequest = ServiceRequest::findOrFail($id);
        $serviceRequest->delete();

        return redirect()
            ->route('admin.requests.index')
            ->with('success', 'Hizmet talebi başarıyla silindi.');
    }
}

