<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderController extends Controller
{
    /**
     * Ustanın iş profilini düzenleme formunu gösterir.
     */
    public function editProfile(): View
    {
        $user = auth()->user();
        $provider = $user->serviceProvider ?? new ServiceProvider();
        $categories = ServiceCategory::orderBy('name')->get();

        return view('provider.profile_edit', compact('user', 'provider', 'categories'));
    }

    /**
     * Ustanın iş profilini (kategori, biyografi, çalışma saatleri vb.) günceller.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        abort_unless((int) $request->user()->role_id === Role::PROVIDER, 403);

        $validated = $request->validate([
            'category_id'   => ['required', 'exists:service_categories,id'],
            'bio'           => ['required', 'string', 'max:2000'],
            'working_hours' => ['nullable', 'string', 'max:255'],
            'company_name'  => ['nullable', 'string', 'max:255'],
        ], [
            'category_id.required' => 'Lütfen bir hizmet kategorisi seçin.',
            'category_id.exists'   => 'Seçilen kategori geçerli değil.',
            'bio.required'         => 'Biyografi alanı zorunludur.',
            'working_hours.string' => 'Çalışma saatleri geçerli bir metin olmalıdır.',
            'company_name.string'  => 'Firma adı geçerli bir metin olmalıdır.',
        ]);

        $user = $request->user();

        $user->serviceProvider()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'category_id'   => $validated['category_id'],
                'bio'           => $validated['bio'],
                'working_hours' => $validated['working_hours'] ?? null,
                'company_name'  => $validated['company_name'] ?? ($user->serviceProvider->company_name ?? $user->name),
            ]
        );

        return redirect()
            ->route('provider.dashboard')
            ->with('success', 'İş profiliniz başarıyla güncellendi.');
    }
}

