<x-admin-layout>
    <x-slot name="header">
        Genel Bakış ve Sistem İstatistikleri
    </x-slot>

    <!-- İstatistik Kartları Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        
        <!-- Toplam Kullanıcı -->
        <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Kullanıcılar</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-gray-900">{{ $stats['total_users'] }}</div>
                <div class="text-[11px] text-gray-500 mt-0.5">
                    <span class="font-bold text-indigo-600">{{ $stats['total_customers'] }}</span> Müşteri, <span class="font-bold text-amber-600">{{ $stats['total_providers'] }}</span> Usta
                </div>
            </div>
        </div>

        <!-- Aktif Talepler -->
        <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Aktif Talepler</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-amber-600">{{ $stats['active_requests'] }}</div>
                <div class="text-[11px] text-gray-500 mt-0.5">Bekleyen / Kabul Edilen</div>
            </div>
        </div>

        <!-- Tamamlanan Randevular -->
        <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Tamamlanan</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-emerald-600">{{ $stats['completed_requests'] }}</div>
                <div class="text-[11px] text-gray-500 mt-0.5">Tamamlanan Hizmet</div>
            </div>
        </div>

        <!-- Toplam Talep -->
        <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Toplam Talep</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-gray-900">{{ $stats['total_requests'] }}</div>
                <div class="text-[11px] text-gray-500 mt-0.5">Tüm Zamanlar</div>
            </div>
        </div>

        <!-- Hizmet Kategorileri -->
        <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Kategoriler</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-purple-600">{{ $stats['total_categories'] }}</div>
                <div class="text-[11px] text-gray-500 mt-0.5"><span class="font-bold text-purple-700">{{ $stats['active_categories'] }}</span> Aktif Kategori</div>
            </div>
        </div>

        <!-- Değerlendirme & Yorumlar -->
        <div class="bg-white rounded-3xl p-5 border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Yorumlar</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-rose-600">{{ $stats['total_reviews'] }}</div>
                <div class="text-[11px] text-gray-500 mt-0.5">Kullanıcı Değerlendirmesi</div>
            </div>
        </div>

    </div>

    <!-- Hızlı Eylemler Çubuğu -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-6 text-white shadow-lg flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-extrabold text-white">Hızlı Yönetim İşlemleri</h3>
            <p class="text-xs text-slate-300 mt-0.5">Sisteme yeni kategori ekleyin, kullanıcıları ve talepleri yönetin.</p>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow transition flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Yeni Kategori Ekle
            </a>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl transition flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Kullanıcıları Yönet
            </a>
            <a href="{{ route('admin.requests.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl transition flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Talepleri İncele
            </a>
        </div>
    </div>

    <!-- Son Aktiviteler 2 Kolon Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Son Hizmet Talepleri -->
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="p-5 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-wider">Son Hizmet Talepleri</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Sisteme iletilen en son talepler.</p>
                </div>
                <a href="{{ route('admin.requests.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                    Tümünü Gör &rarr;
                </a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentRequests as $req)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50/60 transition">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-gray-900">{{ $req->customer->name }}</span>
                                <span class="text-xs text-gray-400">&rarr;</span>
                                <span class="text-xs font-semibold text-indigo-600">{{ $req->provider->company_name ?: $req->provider->user->name }}</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                <span class="px-2 py-0.5 rounded-md bg-gray-100 font-medium text-[10px] mr-1">{{ $req->category->name }}</span>
                                {{ Str::limit($req->description, 40) }}
                            </div>
                        </div>
                        <div>
                            @if($req->status === 'pending')
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-[11px] font-bold">Bekliyor</span>
                            @elseif($req->status === 'accepted')
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[11px] font-bold">Kabul Edildi</span>
                            @elseif($req->status === 'completed')
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-[11px] font-bold">Tamamlandı</span>
                            @else
                                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 rounded-full text-[11px] font-bold">Reddedildi</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-gray-500 italic">Henüz hiç talep bulunmuyor.</div>
                @endforelse
            </div>
        </div>

        <!-- Son Kayıt Olan Kullanıcılar -->
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="p-5 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-wider">Son Kayıt Olan Kullanıcılar</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Sisteme yeni katılan müşteri ve ustalar.</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                    Tümünü Gör &rarr;
                </a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentUsers as $usr)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50/60 transition">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 font-black text-sm flex items-center justify-center">
                                {{ substr($usr->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-sm text-gray-900">{{ $usr->name }}</div>
                                <div class="text-xs text-gray-500">{{ $usr->email }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            @if((int)$usr->role_id === 1)
                                <span class="px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-200 rounded-full text-[10px] font-bold">Admin</span>
                            @elseif((int)$usr->role_id === 3)
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-[10px] font-bold">Usta</span>
                            @else
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full text-[10px] font-bold">Müşteri</span>
                            @endif
                            <div class="text-[10px] text-gray-400 mt-1">{{ $usr->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-gray-500 italic">Henüz hiç kullanıcı bulunmuyor.</div>
                @endforelse
            </div>
        </div>

    </div>
</x-admin-layout>
