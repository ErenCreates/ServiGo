<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\UpdateProfileRequest;
use App\Models\Role;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Güvenlik kontrolü: Giren kişinin rolü Usta olmalı
        abort_unless((int) $request->user()->role_id === Role::PROVIDER, 403);

        $provider = $request->user()->serviceProvider;
        $categories = ServiceCategory::orderBy('name')->get();

        return view('provider.dashboard', compact('provider', 'categories'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        // Not: Yetki kontrolü zaten UpdateProfileRequest içinde authorize() ile yapılıyor.
        // Ekstra güvenlik için burada kalabilir.
        abort_unless((int) $request->user()->role_id === Role::PROVIDER, 403);

        $validated = $request->validated();

        $request->user()->serviceProvider()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'company_name' => $validated['company_name'],
                'bio'          => $validated['bio'],
                'category_id'  => $validated['category_id'],
            ]
        );

        return redirect()
            ->route('provider.dashboard')
            ->with('success', 'Profiliniz başarıyla güncellendi.');
    }
}