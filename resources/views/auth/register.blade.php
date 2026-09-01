<x-guest-layout>
    <div class="bg-white/90 backdrop-blur-md rounded-3xl p-8 sm:p-10 shadow-xl border border-gray-200/80" x-data="{ selectedRole: '{{ old('role_id', '2') }}' }">
        
        <!-- Başlık Bölümü -->
        <div class="text-center mb-6">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                ServiGo'ya Katılın ✨
            </h1>
            <p class="text-sm text-gray-500 mt-2">
                Hizmet almak ya da uzman usta olarak iş fırsatlarına ulaşmak için kaydolun.
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Hesap Türü / Rol Seçimi (Görsel Kartlar) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Hesap Türünüzü Seçin <span class="text-red-500">*</span>
                </label>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Müşteri Seçeneği -->
                    <label 
                        @click="selectedRole = '2'"
                        :class="selectedRole === '2' ? 'border-indigo-600 bg-indigo-50/60 ring-2 ring-indigo-500/20' : 'border-gray-200 bg-gray-50/40 hover:bg-gray-50'"
                        class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition duration-150 ease-in-out"
                    >
                        <input type="radio" name="role_id" value="2" x-model="selectedRole" class="sr-only" required>
                        <div class="flex items-center justify-between mb-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div 
                                x-show="selectedRole === '2'" 
                                class="w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center"
                            >
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <span class="font-bold text-gray-900 text-sm">Müşteriyim</span>
                        <span class="text-xs text-gray-500 mt-0.5">Usta arıyorum, hizmet talebi göndermek istiyorum.</span>
                    </label>

                    <!-- Usta / Hizmet Sağlayıcı Seçeneği -->
                    <label 
                        @click="selectedRole = '3'"
                        :class="selectedRole === '3' ? 'border-indigo-600 bg-indigo-50/60 ring-2 ring-indigo-500/20' : 'border-gray-200 bg-gray-50/40 hover:bg-gray-50'"
                        class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition duration-150 ease-in-out"
                    >
                        <input type="radio" name="role_id" value="3" x-model="selectedRole" class="sr-only" required>
                        <div class="flex items-center justify-between mb-2">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div 
                                x-show="selectedRole === '3'" 
                                class="w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center"
                            >
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                        <span class="font-bold text-gray-900 text-sm">Ustayı / Hizmet Sağlayıcıyım</span>
                        <span class="text-xs text-gray-500 mt-0.5">Hizmet verip müşterilerden iş almak istiyorum.</span>
                    </label>
                </div>
                @error('role_id')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ad Soyad -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Ad Soyad
                </label>
                <div class="relative rounded-xl shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input 
                        id="name" 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus 
                        autocomplete="name"
                        placeholder="Örn: Ahmet Yılmaz" 
                        class="block w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition duration-150 ease-in-out @error('name') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                    >
                </div>
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600 font-medium flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

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

            <!-- Şifre ve Şifre Tekrarı (2 Sütun) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Şifre -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Şifre
                    </label>
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
                            autocomplete="new-password"
                            placeholder="••••••••" 
                            class="block w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition duration-150 ease-in-out @error('password') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                        >
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Şifre Tekrarı -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Şifre Tekrarı
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            placeholder="••••••••" 
                            class="block w-full pl-11 pr-4 py-3 bg-gray-50/50 border border-gray-300 rounded-xl text-sm placeholder-gray-400 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-100 transition duration-150 ease-in-out @error('password_confirmation') border-red-300 focus:border-red-500 focus:ring-red-100 @enderror"
                        >
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kayıt Butonu -->
            <div class="pt-3">
                <button 
                    type="submit" 
                    class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 hover:from-indigo-700 hover:to-indigo-900 text-white font-bold text-sm rounded-xl shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 focus:outline-none focus:ring-4 focus:ring-indigo-300 transform active:scale-[0.99] transition duration-150 ease-in-out cursor-pointer"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Hesap Oluştur ve Başla
                </button>
            </div>
        </form>

        <!-- Giriş Yap Yönlendirme Kartı -->
        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-600">
                Zaten kayıtlı bir hesabınız var mı?
            </p>
            <a 
                href="{{ route('login') }}" 
                class="mt-2 inline-flex items-center justify-center font-bold text-sm text-indigo-600 hover:text-indigo-800 transition"
            >
                Giriş Yapın &rarr;
            </a>
        </div>
    </div>
</x-guest-layout>
