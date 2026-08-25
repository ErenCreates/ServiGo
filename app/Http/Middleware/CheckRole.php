<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Örn: 'admin', 'customer', 'provider'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Giriş yapmamışsa login'e at
        if (! $user) {
            return redirect()->route('login');
        }

        // Role adlarını role_id'ye eşle
        $roleMap = [
            'admin'    => 1,
            'customer' => 2,
            'provider' => 3,
        ];

        $allowedIds = array_map(fn ($r) => $roleMap[$r] ?? null, $roles);

        if (! in_array($user->role_id, $allowedIds, true)) {
            abort(403, 'Bu sayfaya erişim izniniz yok.');
        }

        return $next($request);
    }
}