<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                    {{ __('Hizmet Al & Usta Bul') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    İhtiyacınıza en uygun uzman ustaları keşfedin ve anında hizmet talebi oluşturun.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    {{ $totalProvidersCount }} Aktif Usta Hizmette
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 space-y-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Başarı Bildirimi -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Arama ve Filtreleme Bölümü -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-200/80 space-y-6">
                
                <!-- Arama Çubuğu -->
                <form method="GET" action="{{ route('customer.dashboard') }}" class="relative flex flex-col sm:flex-row gap-3">
                    @if(!empty($selectedCategory) && $selectedCategory !== 'all')
                        <input type="hidden" name="category" value="{{ $selectedCategory }}">
                    @endif

                    <div class="relative flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $searchQuery }}" 
                            placeholder="Usta adı, şirket, hizmet veya anahtar kelime arayın (örn: Elektrik, Su Tesisatı, Ahmet)..." 
                            class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-300 rounded-2xl text-sm placeholder-gray-400 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition duration-150"
                        >
                    </div>

                    <div class="flex gap-2">
                        <button 
                            type="submit" 
                            class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-sm rounded-2xl shadow-md hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-indigo-300 transition duration-150 flex items-center justify-center whitespace-nowrap cursor-pointer"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Usta Ara
                        </button>
                        
                        @if(!empty($searchQuery) || (!empty($selectedCategory) && $selectedCategory !== 'all'))
                            <a 
                                href="{{ route('customer.dashboard') }}" 
                                class="px-4 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm rounded-2xl transition duration-150 flex items-center justify-center whitespace-nowrap"
                                title="Filtreleri Temizle"
                            >
                                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Temizle
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Kategori Filtre Butonları (Pills) -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                            Kategoriye Göre Filtrele
                        </span>
                        @if(!empty($selectedCategory) && $selectedCategory !== 'all')
                            <a href="{{ route('customer.dashboard', array_filter(['search' => $searchQuery])) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                                Tümünü Göster
                            </a>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-2.5">
                        <!-- Tümü Butonu -->
                        <a 
                            href="{{ route('customer.dashboard', array_filter(['search' => $searchQuery])) }}" 
                            class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold transition duration-150 {{ (empty($selectedCategory) || $selectedCategory === 'all') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/25 ring-2 ring-indigo-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-gray-900 border border-transparent' }}"
                        >
                            <span>Tüm Kategoriler</span>
                            <span class="ml-2 px-1.5 py-0.5 rounded-md text-[10px] {{ (empty($selectedCategory) || $selectedCategory === 'all') ? 'bg-indigo-700 text-white' : 'bg-gray-200 text-gray-700' }}">
                                {{ $totalProvidersCount }}
                            </span>
                        </a>

                        <!-- Dinamik Kategoriler -->
                        @foreach($categories as $category)
                            @php
                                $isActive = ((string) $selectedCategory === (string) $category->id || (string) $selectedCategory === $category->name);
                            @endphp
                            <a 
                                href="{{ route('customer.dashboard', array_filter(['category' => $category->id, 'search' => $searchQuery])) }}" 
                                class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold transition duration-150 {{ $isActive ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/25 ring-2 ring-indigo-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-gray-900 border border-transparent' }}"
                            >
                                <span>{{ $category->name }}</span>
                                @if(isset($category->service_providers_count))
                                    <span class="ml-2 px-1.5 py-0.5 rounded-md text-[10px] {{ $isActive ? 'bg-indigo-700 text-white' : 'bg-gray-200 text-gray-700' }}">
                                        {{ $category->service_providers_count }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Usta Kartları Bölümü -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">
                            @if(!empty($selectedCategory) && $selectedCategory !== 'all')
                                {{ $categories->firstWhere('id', $selectedCategory)?->name ?? $selectedCategory }} Alanındaki Ustalar
                            @else
                                Tüm Hizmet Veren Ustalar
                            @endif
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Toplam <span class="font-bold text-gray-800">{{ $providers->count() }}</span> usta listeleniyor
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($providers as $provider)
                        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm hover:shadow-xl hover:border-indigo-200 transition-all duration-200 flex flex-col justify-between overflow-hidden group">
                            
                            <!-- Kart Üst Başlık & Kategori -->
                            <div class="p-6 pb-4">
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <span class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-full border border-indigo-100 uppercase tracking-wider">
                                        {{ $provider->category->name ?? 'Genel Hizmet' }}
                                    </span>

                                    @if(($provider->reviews_count ?? 0) > 0)
                                        <span class="inline-flex items-center text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">
                                            ⭐ {{ number_format($provider->reviews_avg_rating, 1) }} ({{ $provider->reviews_count }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                            <svg class="w-3 h-3 text-emerald-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Doğrulanmış Usta
                                        </span>
                                    @endif
                                </div>

                                <!-- İşletme / Usta Adı -->
                                <h4 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-1">
                                    {{ $provider->company_name ?: $provider->user->name }}
                                </h4>
                                
                                <p class="text-xs text-gray-500 mt-0.5 flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Usta: <span class="font-semibold text-gray-700 ml-1">{{ $provider->user->name }}</span>
                                </p>

                                <!-- Çalışma Saatleri (Varsa) -->
                                @if($provider->working_hours)
                                    <div class="mt-3 flex items-center text-xs text-amber-800 bg-amber-50/80 px-3 py-1.5 rounded-xl border border-amber-200/60">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="truncate">{{ $provider->working_hours }}</span>
                                    </div>
                                @endif

                                <!-- Biyografi -->
                                <p class="text-xs text-gray-600 mt-3 line-clamp-3 leading-relaxed">
                                    {{ $provider->bio ?: 'Bu usta henüz detaylı bir biyografi metni eklemedi.' }}
                                </p>
                            </div>

                            <!-- Kart Alt Çubuğu -->
                            <div class="px-6 py-4 bg-gray-50/60 border-t border-gray-100 flex items-center justify-between mt-auto">
                                <span class="text-xs text-gray-500 font-medium">Hızlı Hizmet</span>
                                <a 
                                    href="{{ route('customer.provider.show', $provider->id) }}" 
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-bold rounded-xl shadow-sm hover:shadow transition duration-150"
                                >
                                    Profili İncele & Talep Gönder &rarr;
                                </a>
                            </div>

                        </div>
                    @empty
                        <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-dashed border-gray-300 shadow-sm space-y-4">
                            <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 mx-auto flex items-center justify-center">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">Aradığınız Kriterlere Uygun Usta Bulunamadı</h4>
                                <p class="text-sm text-gray-500 mt-1 max-w-md mx-auto">
                                    Seçtiğiniz kategori veya arama kelimesine ait aktif usta bulunamadı. Filtreleri temizleyerek diğer ustaları görüntüleyebilirsiniz.
                                </p>
                            </div>
                            <div>
                                <a 
                                    href="{{ route('customer.dashboard') }}" 
                                    class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition"
                                >
                                    Tüm Ustaları Göster
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Müşterinin Gönderdiği Talepler Bölümü -->
            <div class="pt-6">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-200/80 overflow-hidden">
                    <div class="p-6 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-extrabold text-gray-900">Gönderdiğim Hizmet Talepleri</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Ustalara ilettiğiniz taleplerin anlık durum takibi.</p>
                        </div>
                        <span class="text-xs font-bold text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                            {{ $myRequests->count() }} Talep
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50/75 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Usta / İşletme</th>
                                    <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50/75 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50/75 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Talep Detayı</th>
                                    <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50/75 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tarih</th>
                                    <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50/75 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Durum</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($myRequests as $request)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-4 text-sm">
                                            <div class="font-bold text-gray-900">{{ $request->provider->company_name ?: $request->provider->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $request->provider->user->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                {{ $request->category->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ Str::limit($request->description, 60) }}
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                            {{ $request->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                                            @if($request->status == 'pending')
                                                <span class="px-3 py-1 bg-amber-50 text-amber-800 border border-amber-200 rounded-full font-bold text-xs inline-flex items-center">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                                    Bekliyor
                                                </span>
                                            @elseif($request->status == 'accepted')
                                                <span class="px-3 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-full font-bold text-xs inline-flex items-center">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                    Kabul Edildi
                                                </span>
                                            @else
                                                <span class="px-3 py-1 bg-rose-50 text-rose-800 border border-rose-200 rounded-full font-bold text-xs inline-flex items-center">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                                    Reddedildi
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 text-sm">
                                            Henüz gönderdiğiniz bir hizmet talebi bulunmuyor. Yukarıdaki ustalardan birini seçerek hemen talep oluşturabilirsiniz.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
