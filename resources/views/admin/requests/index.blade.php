<x-admin-layout>
    <x-slot name="header">
        Tüm Talepler & Randevular Genel Görünümü
    </x-slot>

    <div class="space-y-6">
        
        <!-- Durum Sayım Kartları / Hızlı Filtreler -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <a 
                href="{{ route('admin.requests.index') }}" 
                class="p-4 rounded-2xl border transition {{ !request('status') || request('status') === 'all' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-200' }}"
            >
                <div class="text-xs font-bold uppercase tracking-wider opacity-80">Tüm Talepler</div>
                <div class="text-xl font-black mt-1">{{ $statusCounts['all'] }}</div>
            </a>

            <a 
                href="{{ route('admin.requests.index', ['status' => 'pending']) }}" 
                class="p-4 rounded-2xl border transition {{ request('status') === 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-200' }}"
            >
                <div class="text-xs font-bold uppercase tracking-wider opacity-80">Bekleyenler</div>
                <div class="text-xl font-black mt-1">{{ $statusCounts['pending'] }}</div>
            </a>

            <a 
                href="{{ route('admin.requests.index', ['status' => 'accepted']) }}" 
                class="p-4 rounded-2xl border transition {{ request('status') === 'accepted' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-200' }}"
            >
                <div class="text-xs font-bold uppercase tracking-wider opacity-80">Kabul Edilenler</div>
                <div class="text-xl font-black mt-1">{{ $statusCounts['accepted'] }}</div>
            </a>

            <a 
                href="{{ route('admin.requests.index', ['status' => 'completed']) }}" 
                class="p-4 rounded-2xl border transition {{ request('status') === 'completed' ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-200' }}"
            >
                <div class="text-xs font-bold uppercase tracking-wider opacity-80">Tamamlananlar</div>
                <div class="text-xl font-black mt-1">{{ $statusCounts['completed'] }}</div>
            </a>

            <a 
                href="{{ route('admin.requests.index', ['status' => 'rejected']) }}" 
                class="p-4 rounded-2xl border transition {{ request('status') === 'rejected' ? 'bg-rose-600 text-white border-rose-600 shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-200' }}"
            >
                <div class="text-xs font-bold uppercase tracking-wider opacity-80">Reddedilenler</div>
                <div class="text-xl font-black mt-1">{{ $statusCounts['rejected'] }}</div>
            </a>
        </div>

        <!-- Filtre ve Arama Çubuğu -->
        <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm">
            <form method="GET" action="{{ route('admin.requests.index') }}" class="flex flex-wrap items-center gap-3">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <div class="relative min-w-[260px] flex-grow max-w-md">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Müşteri, usta veya açıklama ara..." 
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <select 
                    name="category_id" 
                    onchange="this.form.submit()" 
                    class="bg-gray-50 border border-gray-300 rounded-xl text-sm py-2.5 px-3 focus:bg-white focus:border-indigo-600"
                >
                    <option value="">Tüm Kategoriler</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected((string)request('category_id') === (string)$cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-bold transition">
                    Filtrele
                </button>

                @if(request()->hasAny(['search', 'status', 'category_id']))
                    <a href="{{ route('admin.requests.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Filtreleri Temizle
                    </a>
                @endif
            </form>
        </div>

        <!-- Talepler Tablosu -->
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">#ID</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Müşteri</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Usta / İşletme</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Detay</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tarih</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Durum</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($requests as $req)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 text-xs font-bold text-gray-400">
                                    #{{ $req->id }}
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                    {{ $req->customer->name }}
                                    <div class="text-[11px] text-gray-500 font-normal">{{ $req->customer->email }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-indigo-700">
                                    {{ $req->provider->company_name ?: $req->provider->user->name }}
                                    <div class="text-[11px] text-gray-500 font-normal">Usta: {{ $req->provider->user->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-gray-700 whitespace-nowrap">
                                    <span class="px-2.5 py-1 bg-gray-100 rounded-full">
                                        {{ $req->category->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600 max-w-xs">
                                    {{ Str::limit($req->description, 60) }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $req->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <!-- Durum Değiştir Formu -->
                                    <form action="{{ route('admin.requests.update', $req->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <select 
                                            name="status" 
                                            onchange="this.form.submit()" 
                                            class="text-xs font-bold py-1 px-2.5 rounded-full border-0 focus:ring-2 focus:ring-indigo-500 cursor-pointer {{ $req->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($req->status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($req->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800')) }}"
                                        >
                                            <option value="pending" @selected($req->status === 'pending')>Bekliyor</option>
                                            <option value="accepted" @selected($req->status === 'accepted')>Kabul Edildi</option>
                                            <option value="completed" @selected($req->status === 'completed')>Tamamlandı</option>
                                            <option value="rejected" @selected($req->status === 'rejected')>Reddedildi</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right text-xs whitespace-nowrap">
                                    <form action="{{ route('admin.requests.destroy', $req->id) }}" method="POST" onsubmit="return confirm('Bu hizmet talebini silmek istediğinizden emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition"
                                        >
                                            Sil
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500 text-sm italic">
                                    Kayıtlı hizmet talebi bulunamadı.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Sayfalama -->
            @if($requests->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>

    </div>
</x-admin-layout>

