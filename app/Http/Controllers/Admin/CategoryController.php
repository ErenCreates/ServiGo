<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Kategori listesi ve yönetim ekranı.
     */
    public function index(Request $request): View
    {
        $query = ServiceCategory::withCount('serviceProviders');

        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $categories = $query->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Yeni kategori ekler.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:service_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Kategori adı zorunludur.',
            'name.unique'   => 'Bu isimde bir kategori zaten mevcut.',
        ]);

        ServiceCategory::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori başarıyla oluşturuldu.');
    }

    /**
     * Kategori bilgilerini günceller.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $category = ServiceCategory::findOrFail($id);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:service_categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Kategori adı zorunludur.',
            'name.unique'   => 'Bu isimde bir kategori zaten mevcut.',
        ]);

        $category->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->has('is_active') ? (bool) $request->is_active : false,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori başarıyla güncellendi.');
    }

    /**
     * Kategorinin aktif / pasif durumunu değiştirir.
     */
    public function toggleStatus($id): RedirectResponse
    {
        $category = ServiceCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        $statusText = $category->is_active ? 'aktif duruma getirildi' : 'pasife alındı';
        return redirect()
            ->back()
            ->with('success', "'{$category->name}' kategorisi başarıyla {$statusText}.");
    }

    /**
     * Kategoriyi siler.
     */
    public function destroy($id): RedirectResponse
    {
        $category = ServiceCategory::findOrFail($id);
        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "'{$name}' kategorisi sistemden silindi.");
    }
}

