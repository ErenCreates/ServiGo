<x-guest-layout>
    <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 sm:p-10 shadow-xl border border-gray-200/80">
        
        <!-- Başlık Bölümü -->
        <div class="text-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                Tekrar Hoş Geldiniz! 👋
            </h1>
            <p class="text-sm text-gray-500 mt-2">
                Hizmet taleplerinizi veya usta panelinizi yönetmek için giriş yapın.
            </p>
        </div>

        <!-- Oturum Durum Bildirimi -->
        <x-auth-session-status class="mb-5" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- E-Posta Adresi -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    E-Posta Adresi
                </label>
                <div class="relative rounded-xl shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                        </svg>
                    </div>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                        placeholder="ornek@servigo.com" 
                        class="block w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition duration-150 ease-in-out @error('email') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                    >
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Şifre -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-semibold text-gray-700">
                        Şifre
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                            Şifremi Unuttum?
                        </a>
                    @endif
                </div>
                <div class="relative rounded-xl shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••" 
                        class="block w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition duration-150 ease-in-out @error('password') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                    >
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Beni Hatırla -->
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember"
                        class="w-4 h-4 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-0 cursor-pointer"
                    >
                    <span class="ms-2 text-sm text-gray-600 font-medium select-none">Beni hatırla</span>
                </label>
            </div>

            <!-- Giriş Butonu -->
            <div class="pt-2">
                <button 
                    type="submit" 
                    class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 hover:from-indigo-700 hover:to-indigo-900 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 focus:outline-none focus:ring-4 focus:ring-indigo-300 transform active:scale-[0.99] transition duration-150 ease-in-out cursor-pointer"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Giriş Yap
                </button>
            </div>
        </form>

        <!-- Kayıt Ol Yönlendirme Kartı -->
        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-600">
                Henüz bir ServiGo hesabınız yok mu?
            </p>
            <a 
                href="{{ route('register') }}" 
                class="mt-2 inline-flex items-center justify-center font-bold text-sm text-indigo-600 hover:text-indigo-800 transition"
            >
                Hemen Ücretsiz Kayıt Olun &rarr;
            </a>
        </div>
    </div>
</x-guest-layout>
