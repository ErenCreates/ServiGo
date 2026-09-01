<x-admin-layout>
    <x-slot name="header">
        Kullanıcı ve Usta Yönetimi
    </x-slot>

    <div class="space-y-6">
        
        <!-- Filtreleme ve Arama Çubuğu -->
        <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3">
                
                <!-- Arama Kutusu -->
                <div class="relative min-w-[260px] flex-grow max-w-md">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="İsim, e-posta veya işletme adı ara..." 
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <!-- Rol Filtresi -->
                <select 
                    name="role" 
                    onchange="this.form.submit()" 
                    class="bg-gray-50 border border-gray-300 rounded-xl text-sm py-2.5 px-3 focus:bg-white focus:border-indigo-600"
                >
                    <option value="">Tüm Roller</option>
                    <option value="customer" @selected(request('role') === 'customer')>Sadece Müşteriler</option>
                    <option value="provider" @selected(request('role') === 'provider')>Sadece Ustalar</option>
                    <option value="admin" @selected(request('role') === 'admin')>Sadece Yöneticiler</option>
                </select>

                <!-- Durum Filtresi -->
                <select 
                    name="status" 
                    onchange="this.form.submit()" 
                    class="bg-gray-50 border border-gray-300 rounded-xl text-sm py-2.5 px-3 focus:bg-white focus:border-indigo-600"
                >
                    <option value="">Tüm Durumlar</option>
                    <option value="active" @selected(request('status') === 'active')>Sadece Aktifler</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Sadece Askıya Alınanlar</option>
                </select>

                <button type="submit" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-bold transition">
                    Filtrele
                </button>

                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Filtreleri Temizle
                    </a>
                @endif
            </form>
        </div>

        <!-- Kullanıcılar Tablosu -->
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kullanıcı Bilgileri</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Hesap Rolü</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Usta / İşletme Detayı</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Durum</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kayıt Tarihi</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50/50 transition">
                                <!-- Kullanıcı -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-800 font-black text-sm flex items-center justify-center flex-shrink-0">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-sm text-gray-900 flex items-center gap-1.5">
                                                <span>{{ $user->name }}</span>
                                                @if($user->id === auth()->id())
                                                    <span class="text-[10px] bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded font-bold">Siz</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Rol -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if((int)$user->role_id === 1)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            Yönetici (Admin)
                                        </span>
                                    @elseif((int)$user->role_id === 3)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Usta / Sağlayıcı
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Müşteri
                                        </span>
                                    @endif
                                </td>

                                <!-- Usta Bilgisi -->
                                <td class="px-6 py-4 text-xs text-gray-600">
                                    @if($user->serviceProvider)
                                        <div>
                                            <span class="font-bold text-gray-900">{{ $user->serviceProvider->company_name ?: '-' }}</span>
                                            @if($user->serviceProvider->category)
                                                <div class="text-indigo-600 font-semibold">{{ $user->serviceProvider->category->name }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <!-- Durum -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->is_active ?? true)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                            Askıya Alındı
                                        </span>
                                    @endif
                                </td>

                                <!-- Tarih -->
                                <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $user->created_at->format('d.m.Y H:i') }}
                                </td>

                                <!-- İşlemler -->
                                <td class="px-6 py-4 text-right text-xs whitespace-nowrap">
                                    @if($user->id !== auth()->id())
                                        <div class="flex items-center justify-end space-x-2">
                                            
                                            <!-- Askıya Al / Aktifleştir -->
                                            <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button 
                                                    type="submit" 
                                                    title="{{ ($user->is_active ?? true) ? 'Hesabı Askıya Al' : 'Hesabı Aktifleştir' }}"
                                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold {{ ($user->is_active ?? true) ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }} transition"
                                                >
                                                    {{ ($user->is_active ?? true) ? 'Askıya Al' : 'Aktifleştir' }}
                                                </button>
                                            </form>

                                            <!-- Hesabı Sil -->
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ $user->name }} kullanıcısını ve tüm verilerini silmek istediğinizden emin misiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition"
                                                >
                                                    Sil
                                                </button>
                                            </form>

                                        </div>
                                    @else
                                        <span class="text-gray-400 italic text-[11px]">Yönetici</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm italic">
                                    Kriterlere uygun kullanıcı bulunamadı.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Sayfalama (Pagination) -->
            @if($users->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>
</x-admin-layout>

