<x-admin-layout>
    <x-slot name="header">
        Yorumlar & Değerlendirmeler Yönetimi
    </x-slot>

    <div class="space-y-6">
        
        <!-- İstatistik Çubuğu -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-wider text-gray-500">Toplam Yorum</div>
                <div class="text-xl font-black text-gray-900 mt-1">{{ $stats['total'] }}</div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-wider text-emerald-600">Yayında / Onaylı</div>
                <div class="text-xl font-black text-emerald-600 mt-1">{{ $stats['approved'] }}</div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-wider text-amber-600">Onay Bekleyen / Gizli</div>
                <div class="text-xl font-black text-amber-600 mt-1">{{ $stats['pending'] }}</div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
                <div class="text-xs font-bold uppercase tracking-wider text-indigo-600">Ortalama Puan</div>
                <div class="text-xl font-black text-indigo-600 mt-1">⭐ {{ $stats['avg'] }} / 5</div>
            </div>
        </div>

        <!-- Filtre ve Arama Çubuğu -->
        <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm">
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-wrap items-center gap-3">
                
                <div class="relative min-w-[260px] flex-grow max-w-md">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Yorum, müşteri veya usta adı ara..." 
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <select 
                    name="status" 
                    onchange="this.form.submit()" 
                    class="bg-gray-50 border border-gray-300 rounded-xl text-sm py-2.5 px-3 focus:bg-white focus:border-indigo-600"
                >
                    <option value="">Tüm Durumlar</option>
                    <option value="approved" @selected(request('status') === 'approved')>Sadece Yayında Olanlar</option>
                    <option value="pending" @selected(request('status') === 'pending')>Sadece Gizli / Onaysızlar</option>
                </select>

                <select 
                    name="rating" 
                    onchange="this.form.submit()" 
                    class="bg-gray-50 border border-gray-300 rounded-xl text-sm py-2.5 px-3 focus:bg-white focus:border-indigo-600"
                >
                    <option value="">Tüm Puanlar</option>
                    <option value="5" @selected((string)request('rating') === '5')>⭐⭐⭐⭐⭐ (5 Yıldız)</option>
                    <option value="4" @selected((string)request('rating') === '4')>⭐⭐⭐⭐ (4 Yıldız)</option>
                    <option value="3" @selected((string)request('rating') === '3')>⭐⭐⭐ (3 Yıldız)</option>
                    <option value="2" @selected((string)request('rating') === '2')>⭐⭐ (2 Yıldız)</option>
                    <option value="1" @selected((string)request('rating') === '1')>⭐ (1 Yıldız)</option>
                </select>

                <button type="submit" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-bold transition">
                    Filtrele
                </button>

                @if(request()->hasAny(['search', 'status', 'rating']))
                    <a href="{{ route('admin.reviews.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Temizle
                    </a>
                @endif
            </form>
        </div>

        <!-- Yorumlar Tablosu -->
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Puan</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Müşteri</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Usta / Hizmet</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Yorum Detayı</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tarih</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Durum</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($reviews as $review)
                            <tr class="hover:bg-gray-50/50 transition">
                                <!-- Puan -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center text-amber-400 text-sm">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                ★
                                            @else
                                                <span class="text-gray-200">★</span>
                                            @endif
                                        @endfor
                                        <span class="text-xs font-bold text-gray-700 ml-1.5">({{ $review->rating }}/5)</span>
                                    </div>
                                </td>

                                <!-- Müşteri -->
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 whitespace-nowrap">
                                    {{ $review->customer->name }}
                                    <div class="text-xs text-gray-400 font-normal">{{ $review->customer->email }}</div>
                                </td>

                                <!-- Usta -->
                                <td class="px-6 py-4 text-sm font-bold text-indigo-700 whitespace-nowrap">
                                    {{ $review->provider->company_name ?: $review->provider->user->name }}
                                    <div class="text-xs text-gray-500 font-normal">Usta: {{ $review->provider->user->name }}</div>
                                </td>

                                <!-- Yorum -->
                                <td class="px-6 py-4 text-xs text-gray-700 max-w-sm leading-relaxed">
                                    "{{ $review->comment }}"
                                </td>

                                <!-- Tarih -->
                                <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $review->created_at->format('d.m.Y H:i') }}
                                </td>

                                <!-- Durum -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($review->is_approved)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Yayında
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Gizli / Onaysız
                                        </span>
                                    @endif
                                </td>

                                <!-- İşlemler -->
                                <td class="px-6 py-4 text-right text-xs whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2">
                                        
                                        <!-- Onayla / Gizle Butonu -->
                                        <form action="{{ route('admin.reviews.toggle', $review->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit" 
                                                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold {{ $review->is_approved ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }} transition"
                                            >
                                                {{ $review->is_approved ? 'Gizle' : 'Yayınla' }}
                                            </button>
                                        </form>

                                        <!-- Sil Butonu -->
                                        <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Bu yorumu silmek istediğinizden emin misiniz?');">
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 text-sm italic">
                                    Kayıtlı yorum veya değerlendirme bulunamadı.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Sayfalama -->
            @if($reviews->hasPages())
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>

    </div>
</x-admin-layout>

