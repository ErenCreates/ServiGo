<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ServiGo') }} - Güvenilir Usta ve Hizmet Platformu</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 bg-slate-50 min-h-screen flex flex-col justify-between selection:bg-indigo-500 selection:text-white">
        
        <!-- Ambient Decorative Background Gradients -->
        <div class="fixed inset-0 pointer-events-none -z-10 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-indigo-200/40 blur-3xl"></div>
            <div class="absolute top-1/3 -left-40 w-96 h-96 rounded-full bg-amber-200/30 blur-3xl"></div>
            <div class="absolute -bottom-40 right-1/4 w-96 h-96 rounded-full bg-blue-200/30 blur-3xl"></div>
        </div>

        <!-- Üst Başlık / Navigasyon -->
        <header class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-between">
            <a href="/" class="transition transform hover:scale-105 duration-200">
                <x-application-logo />
            </a>
            <div class="flex items-center space-x-3 text-sm">
                @if (Route::has('login') && request()->routeIs('register'))
                    <span class="text-gray-500 hidden sm:inline">Zaten bir hesabınız var mı?</span>
                    <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition py-1.5 px-3 rounded-lg hover:bg-indigo-50">
                        Giriş Yap
                    </a>
                @elseif (Route::has('register') && request()->routeIs('login'))
                    <span class="text-gray-500 hidden sm:inline">Hesabınız yok mu?</span>
                    <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition py-1.5 px-3 rounded-lg hover:bg-indigo-50">
                        Kayıt Ol
                    </a>
                @else
                    <a href="/" class="text-gray-600 hover:text-gray-900 font-medium text-sm transition">
                        &larr; Ana Sayfa
                    </a>
                @endif
            </div>
        </header>

        <!-- Ana İçerik -->
        <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-6">
            <div class="w-full max-w-md sm:max-w-lg">
                {{ $slot }}
            </div>
        </main>

        <!-- Alt Bilgi (Footer) -->
        <footer class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-xs text-gray-500">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 border-t border-gray-200/80 pt-4">
                <p>&copy; {{ date('Y') }} ServiGo Hizmet Pazaryeri. Tüm hakları saklıdır.</p>
                <div class="flex space-x-4 text-gray-400">
                    <span class="inline-flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        256-Bit SSL Güvenli Bağlantı
                    </span>
                </div>
            </div>
        </footer>

    </body>
</html>
