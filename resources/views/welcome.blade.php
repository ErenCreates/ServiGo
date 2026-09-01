<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ServiGo') }} - Güvenilir Usta & Hizmet Platformu</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-gray-900 selection:bg-indigo-600 selection:text-white min-h-screen flex flex-col justify-between">

        <!-- Arka Plan Dekoratif Işıklar -->
        <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-indigo-200/40 blur-3xl"></div>
            <div class="absolute top-1/3 -left-40 w-96 h-96 rounded-full bg-amber-200/30 blur-3xl"></div>
            <div class="absolute -bottom-40 right-1/4 w-96 h-96 rounded-full bg-blue-200/30 blur-3xl"></div>
        </div>

        <!-- Üst Navigasyon Çubuğu -->
        <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-gray-100 transition duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-2 transition transform hover:scale-105">
                    <x-application-logo />
                </a>

                <!-- Navigasyon Linkleri (Masaüstü) -->
                <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-gray-600">
                    <a href="#kategoriler" class="hover:text-indigo-600 transition">Hizmetler</a>
                    <a href="#nasil-calisir" class="hover:text-indigo-600 transition">Nasıl Çalışır?</a>
                    <a href="#ustalar" class="hover:text-indigo-600 transition">Usta Olun</a>
                </nav>

                <!-- Auth Butonları -->
                <div class="flex items-center space-x-3">
                    @auth
                        <a 
                            href="{{ url('/dashboard') }}" 
                            class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-extrabold uppercase tracking-wider rounded-xl shadow-md hover:shadow-lg transition"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Panele Git
                        </a>
                    @else
                        <a 
                            href="{{ route('login') }}" 
                            class="text-xs font-bold text-gray-700 hover:text-indigo-600 px-4 py-2.5 rounded-xl hover:bg-gray-100 transition"
                        >
                            Giriş Yap
                        </a>

                        @if (Route::has('register'))
                            <a 
                                href="{{ route('register') }}" 
                                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-extrabold uppercase tracking-wider rounded-xl shadow-md hover:shadow-lg transition"
                            >
                                Kayıt Ol
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <!-- Ana Bölüm (Hero) -->
        <main class="flex-grow">
            <section class="relative pt-12 pb-20 md:pt-20 md:pb-28 overflow-hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                        
                        <!-- Sol Kolon: Başlık ve CTA -->
                        <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                            <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold shadow-sm">
                                <span class="flex h-2 w-2 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span>Türkiye'nin Yeni Nesil Usta & Hizmet Platformu</span>
                            </div>

                            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-gray-900 tracking-tight leading-[1.15]">
                                Eviniz ve İş Yeriniz İçin <br>
                                <span class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-amber-500 bg-clip-text text-transparent">
                                    Güvenilir Ustalar
                                </span> Bir Tık Uzağınızda.
                            </h1>

                            <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                                Elektrikten su tesisatına, boya badanadan mobilya montajına kadar tüm tamirat ve tadilat ihtiyaçlarınız için onaylı ustaları keşfedin, doğrudan randevu planlayın.
                            </p>

                            <!-- Butonlar -->
                            <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                                <a 
                                    href="{{ auth()->check() ? route('customer.dashboard') : route('register') }}" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-extrabold text-sm uppercase tracking-wider rounded-2xl shadow-lg shadow-indigo-600/30 hover:shadow-xl transition"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Hemen Usta Bul
                                </a>

                                <a 
                                    href="{{ auth()->check() ? (auth()->user()->isProvider() ? route('provider.dashboard') : route('customer.dashboard')) : route('register') }}" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-4 bg-white hover:bg-gray-50 active:bg-gray-100 text-gray-800 font-bold text-sm rounded-2xl border border-gray-200 shadow-sm hover:border-gray-300 transition"
                                >
                                    <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Usta Olarak Katıl
                                </a>
                            </div>

                            <!-- Güven İstatistikleri Rozetleri -->
                            <div class="grid grid-cols-3 gap-4 pt-6 border-t border-gray-200/80 max-w-lg mx-auto lg:mx-0">
                                <div>
                                    <div class="text-2xl sm:text-3xl font-black text-gray-900">{{ max($stats['providers'] ?? 0, 12) }}+</div>
                                    <div class="text-xs text-gray-500 font-medium">Kayıtlı Usta</div>
                                </div>
                                <div>
                                    <div class="text-2xl sm:text-3xl font-black text-indigo-600">{{ max($stats['completed'] ?? 0, 48) }}+</div>
                                    <div class="text-xs text-gray-500 font-medium">Tamamlanan İş</div>
                                </div>
                                <div>
                                    <div class="text-2xl sm:text-3xl font-black text-amber-500">4.9 ★</div>
                                    <div class="text-xs text-gray-500 font-medium">Müşteri Memnuniyeti</div>
                                </div>
                            </div>
                        </div>

                        <!-- Sağ Kolon: Görsel / Kart Vitrini -->
                        <div class="lg:col-span-5 relative">
                            <div class="relative mx-auto max-w-md bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-gray-100 space-y-6">
                                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-lg">
                                            SG
                                        </div>
                                        <div>
                                            <h4 class="font-extrabold text-gray-900 text-sm">Hızlı Hizmet Talebi</h4>
                                            <p class="text-xs text-gray-400">Dakikalar içinde randevu planlayın</p>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[11px] font-bold rounded-full">
                                        Canlı
                                    </span>
                                </div>

                                <!-- Örnek Usta Kartı -->
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2.5 py-0.5 bg-indigo-100 text-indigo-800 rounded-md text-[10px] font-extrabold uppercase">
                                            Elektrik & Tesisat
                                        </span>
                                        <span class="text-xs font-bold text-amber-600">⭐ 4.9 (18 Yorum)</span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-sm text-gray-900">Usta Elektrik Hizmetleri</div>
                                        <p class="text-xs text-gray-500 mt-0.5">15 yıllık tecrübe ile garantili arıza onarım ve tesisat.</p>
                                    </div>
                                    <div class="flex items-center justify-between text-xs text-gray-500 pt-2 border-t border-slate-200/60">
                                        <span>🕒 Hafta içi 09:00 - 18:00</span>
                                        <span class="text-indigo-600 font-bold">Randevu Alınabilir</span>
                                    </div>
                                </div>

                                <!-- 3 Avantaj Maddesi -->
                                <div class="space-y-2.5 text-xs text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-emerald-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Doğrulanmış ve Puanlanmış Ustalar
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-emerald-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Şeffaf Müşteri Yorumları
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-emerald-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Kolay Takvim ve Randevu Takibi
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- Popüler Hizmet Kategorileri -->
            <section id="kategoriler" class="py-16 bg-white border-y border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                    <div class="text-center max-w-2xl mx-auto space-y-3">
                        <h2 class="text-xs font-extrabold uppercase tracking-widest text-indigo-600">Popüler Hizmetler</h2>
                        <h3 class="text-3xl font-black text-gray-900">İhtiyacınıza Uygun Hizmet Kategorisi Seçin</h3>
                        <p class="text-sm text-gray-500">Alanında deneyimli ustalarımız bir tık uzağınızda hizmet vermeye hazır.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($categories as $cat)
                            <a 
                                href="{{ auth()->check() ? route('customer.dashboard', ['category' => $cat->id]) : route('login') }}" 
                                class="p-6 rounded-3xl bg-slate-50 hover:bg-indigo-50/50 border border-slate-100 hover:border-indigo-200 transition duration-200 flex flex-col justify-between group shadow-sm hover:shadow-md"
                            >
                                <div class="space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-600/10 text-indigo-600 flex items-center justify-center font-bold text-xl group-hover:bg-indigo-600 group-hover:text-white transition">
                                        🛠️
                                    </div>
                                    <h4 class="text-lg font-extrabold text-gray-900 group-hover:text-indigo-600 transition">
                                        {{ $cat->name }}
                                    </h4>
                                    <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                                        {{ $cat->description ?: 'Bu kategoride kaliteli usta hizmetleri sunulmaktadır.' }}
                                    </p>
                                </div>

                                <div class="mt-5 pt-4 border-t border-slate-200/60 flex items-center justify-between text-xs font-bold text-indigo-600">
                                    <span>{{ $cat->service_providers_count ?? 0 }} Aktif Usta</span>
                                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                                </div>
                            </a>
                        @empty
                            <div class="col-span-full text-center py-10 text-gray-400 text-sm italic">
                                Henüz kategori bulunmuyor.
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>

            <!-- Nasıl Çalışır? -->
            <section id="nasil-calisir" class="py-20 bg-slate-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                    <div class="text-center max-w-2xl mx-auto space-y-3">
                        <h2 class="text-xs font-extrabold uppercase tracking-widest text-indigo-600">Kolay ve Hızlı Süreç</h2>
                        <h3 class="text-3xl font-black text-gray-900">ServiGo Nasıl Çalışır?</h3>
                        <p class="text-sm text-gray-500">Yalnızca 3 basit adımda istediğiniz usta ile randevunuzu oluşturun.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Adım 1 -->
                        <div class="bg-white rounded-3xl p-8 border border-gray-200 shadow-sm relative space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-lg shadow-md">
                                1
                            </div>
                            <h4 class="text-lg font-extrabold text-gray-900">Ustanı Seç & Talep Gönder</h4>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                Kategori veya arama filtrelerini kullanarak ustanızı belirleyin, yapılması gereken işi detaylarıyla yazıp talebinizi iletin.
                            </p>
                        </div>

                        <!-- Adım 2 -->
                        <div class="bg-white rounded-3xl p-8 border border-gray-200 shadow-sm relative space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-black text-lg shadow-md">
                                2
                            </div>
                            <h4 class="text-lg font-extrabold text-gray-900">Randevu Zamanını Onayla</h4>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                Usta talebinizi inceler, randevu tarih ve saatini belirler. "Randevularım" sayfasından takviminizi anlık olarak takip edin.
                            </p>
                        </div>

                        <!-- Adım 3 -->
                        <div class="bg-white rounded-3xl p-8 border border-gray-200 shadow-sm relative space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black text-lg shadow-md">
                                3
                            </div>
                            <h4 class="text-lg font-extrabold text-gray-900">Hizmeti Al & Değerlendir</h4>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                İş tamamlandıktan sonra ustanın işçiliğini 1-5 arası yıldızla puanlayın ve yorum yaparak diğer müşterilere rehber olun.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Usta Katılım CTA -->
            <section id="ustalar" class="py-16 bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
                    <div class="inline-flex items-center px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-xs font-bold backdrop-blur-sm">
                        💼 Usta & İşletmelere Özel
                    </div>

                    <h3 class="text-3xl sm:text-4xl font-black max-w-2xl mx-auto leading-tight">
                        İşinizi Büyütün, Yeni Müşterilere ServiGo ile Ulaşın
                    </h3>

                    <p class="text-sm text-indigo-200 max-w-xl mx-auto leading-relaxed">
                        Binlerce müşterinin hizmet aradığı platformumuzda profilinizi oluşturun, çalışma saatlerinizi ve kategorinizi belirleyin, gelen talepleri randevuya dönüştürün.
                    </p>

                    <div class="pt-4">
                        <a 
                            href="{{ route('register') }}" 
                            class="inline-flex items-center px-8 py-4 bg-amber-500 hover:bg-amber-400 active:bg-amber-600 text-gray-900 font-extrabold text-sm uppercase tracking-wider rounded-2xl shadow-xl transition"
                        >
                            Hizmet Veren Olarak Kaydol
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <!-- Alt Bilgi (Footer) -->
        <footer class="bg-white border-t border-gray-200/80 py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center space-x-2">
                        <x-application-logo />
                    </div>
                    <p class="text-xs text-gray-500">
                        &copy; {{ date('Y') }} ServiGo Hizmet ve Usta Platformu. Tüm hakları saklıdır.
                    </p>
                    <div class="flex items-center space-x-4 text-xs font-bold text-gray-600">
                        <span class="inline-flex items-center text-emerald-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            256-Bit SSL Güvenli Bağlantı
                        </span>
                    </div>
                </div>
            </div>
        </footer>

    </body>
</html>
