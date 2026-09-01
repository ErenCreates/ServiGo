<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Kullanıcı ve usta listesi yönetim ekranı.
     */
    public function index(Request $request): View
    {
        $query = User::with(['role', 'serviceProvider.category']);

        // Rol Filtresi
        if ($request->filled('role')) {
            $roleId = match ($request->role) {
                'customer' => Role::CUSTOMER,
                'provider' => Role::PROVIDER,
                'admin'    => Role::ADMIN,
                default    => is_numeric($request->role) ? (int) $request->role : null,
            };

            if ($roleId) {
                $query->where('role_id', $roleId);
            }
        }

        // Durum Filtresi (Aktif / Pasif)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Arama (İsim, E-posta veya İşletme Adı)
        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhereHas('serviceProvider', function ($spq) use ($search) {
                      $spq->where('company_name', 'like', $search);
                  });
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Kullanıcının veya ustanın aktif / askıya alınmış durumunu değiştirir.
     */
    public function toggleStatus($id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Kendi admin hesabını pasife almasını engelle
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'Kendi yönetici hesabınızı askıya alamazsınız.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'aktif duruma getirildi' : 'askıya alındı (pasifleştirildi)';
        return redirect()
            ->back()
            ->with('success', "{$user->name} ({$user->email}) hesabı başarıyla {$statusText}.");
    }

    /**
     * Kural ihlali yapan kullanıcıyı veya ustayı sistemden siler.
     */
    public function destroy($id): RedirectResponse
    {
        $user = User::findOrFail($id);

        // Kendi admin hesabını silmesini engelle
        if ($user->id === auth()->id()) {
            return redirect()
                ->back()
                ->with('error', 'Kendi yönetici hesabınızı silemezsiniz.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$name} kullanıcısı sistemden başarıyla silindi.");
    }
}

