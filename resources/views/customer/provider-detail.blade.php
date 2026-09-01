<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-xl shadow-md">
                    {{ substr($provider->company_name ?: $provider->user->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                        {{ $provider->company_name ?: $provider->user->name }}
                    </h2>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mt-0.5">
                        <span>Usta: <strong class="text-gray-700">{{ $provider->user->name }}</strong></span>
                        <span>•</span>
                        <span class="text-indigo-600 font-bold">{{ $provider->category->name }}</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                &larr; Usta Aramaya Dön
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Bildirimler -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Üst Puan ve Bilgi Özeti Bannerı -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 font-bold text-xs rounded-full uppercase tracking-wider">
                        {{ $provider->category->name }}
                    </span>
                    <h3 class="text-xl font-black text-gray-900 mt-2">{{ $provider->company_name ?: $provider->user->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1 max-w-lg leading-relaxed">{{ $provider->bio ?: 'Usta henüz detaylı biyografi eklememiş.' }}</p>
                    
                    @if($provider->working_hours)
                        <div class="flex items-center text-xs text-gray-600 mt-3 font-medium">
                            <svg class="w-4 h-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Çalışma Saatleri: <span class="font-bold text-gray-800 ml-1">{{ $provider->working_hours }}</span>
                        </div>
                    @endif
                </div>

                <!-- Ortalama Puan Kutusu -->
                <div class="flex-shrink-0 bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200/80 rounded-3xl p-6 text-center min-w-[200px]">
                    <div class="text-3xl font-black text-amber-600">
                        {{ $averageRating > 0 ? number_format($averageRating, 1) : '-' }}
                    </div>
                    <div class="flex items-center justify-center text-amber-400 text-lg mt-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($averageRating))
                                ★
                            @else
                                <span class="text-gray-300">★</span>
                            @endif
                        @endfor
                    </div>
                    <div class="text-xs font-bold text-gray-600 mt-1">
                        @if($totalReviews > 0)
                            {{ $totalReviews }} Müşteri Değerlendirmesi
                        @else
                            Henüz Puanlanmadı
                        @endif
                    </div>
                </div>
            </div>

            <!-- Müşteri Değerlendirmeleri & Yorumları Bölümü -->
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-gray-900">Müşteri Yorumları ve Değerlendirmeleri</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Bu ustadan daha önce hizmet almış müşterilerin yorumları.</p>
                    </div>
                    <span class="px-3 py-1 bg-white border border-gray-200 rounded-full text-xs font-bold text-gray-700">
                        {{ $totalReviews }} Yorum
                    </span>
                </div>

                <div class="divide-y divide-gray-100 p-6 space-y-4">
                    @forelse($provider->reviews as $review)
                        <div class="pt-4 first:pt-0">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 font-black text-xs flex items-center justify-center">
                                        {{ substr($review->customer->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-sm text-gray-900">{{ $review->customer->name }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $review->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center text-amber-400 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            ★
                                        @else
                                            <span class="text-gray-200">★</span>
                                        @endif
                                    @endfor
                                    <span class="text-xs font-bold text-gray-700 ml-1">({{ $review->rating }}/5)</span>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-gray-700 leading-relaxed pl-12">
                                "{{ $review->comment }}"
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-gray-500 italic">
                            Bu usta için henüz bir değerlendirme veya yorum yapılmamış. Tamamlanan ilk randevunuz sonrası ilk yorumu siz yapabilirsiniz!
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Hizmet Talebi Oluşturma Formu -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-sm space-y-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Ustayla İletişime Geç & Talep Gönder</h3>
                    <p class="text-xs text-gray-500 mt-0.5">İhtiyacınız olan hizmeti açıklayın, usta incelesin ve size randevu belirlesin.</p>
                </div>

                <form action="{{ route('customer.request.store', $provider->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">
                            Yapılacak İş / Hizmet Detayı <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="4" 
                            required
                            minlength="10"
                            placeholder="Örn: Salon ve mutfaktaki prizlerde elektrik kesintisi var. Sigorta kutusunun kontrol edilmesi gerekiyor..." 
                            class="w-full rounded-2xl border-gray-300 focus:border-indigo-600 focus:ring-indigo-100 text-sm p-3"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="text-right">
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Hizmet Talebini Gönder
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>