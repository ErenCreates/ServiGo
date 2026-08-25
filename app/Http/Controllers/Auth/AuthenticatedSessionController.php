<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Giriş başarılı olunca kullanıcıyı rolüne uygun sayfaya gönderiyoruz
        return redirect($this->redirectPath($request->user()));
    }

    /**
     * Rol bazlı URL belirleme fonksiyonu
     */
    private function redirectPath($user): string
    {
        return match ((int) $user->role_id) {
            1 => route('admin.dashboard'),
            2 => route('customer.dashboard'),
            3 => route('provider.dashboard'),
            default => route('login'),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}