<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('İş Profilini Düzenle') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Hizmet kategorinizi, çalışma saatlerinizi ve müşterilerinize görünecek biyografi bilgilerinizi güncelleyin.
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('provider.dashboard') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Usta Paneline Dön
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Başarı Bildirimi -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 flex items-center shadow-sm">
                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Hata Bildirimi -->
            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 shadow-sm">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-bold">Lütfen formdaki hataları düzeltin:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-200/80 overflow-hidden">
                <!-- Kart Başlığı -->
                <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Usta İş Profili Formu</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Müşteriler bu bilgileri inceleyerek size talep oluşturacaktır.</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        Usta Hesabı
                    </span>
                </div>

                <form action="{{ route('provider.profile.update') }}" method="POST" class="p-6 sm:p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Firma / Ünvan -->
                    <div>
                        <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-1">
                            Şirket / Marka / Usta Ünvanı
                        </label>
                        <div class="relative rounded-lg shadow-sm">
                            <input 
                                type="text" 
                                name="company_name" 
                                id="company_name" 
                                value="{{ old('company_name', $provider->company_name ?? auth()->user()->name) }}" 
                                placeholder="Örn: Ahmet Usta Tesisat & Tamirat"
                                class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3.5"
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Profilinizde ve arama sonuçlarında görünecek işletme veya usta adınız.</p>
                        @error('company_name')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hizmet Kategorisi -->
                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1">
                            Hizmet Kategorisi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-lg shadow-sm">
                            <select 
                                name="category_id" 
                                id="category_id" 
                                class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3.5 bg-white @error('category_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                required
                            >
                                <option value="" disabled {{ empty(old('category_id', $provider->category_id ?? '')) ? 'selected' : '' }}>-- Bir Hizmet Kategorisi Seçin --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (old('category_id', $provider->category_id ?? '') == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Uzman olduğunuz ve müşterilerden talep almak istediğiniz ana kategori.</p>
                        @error('category_id')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Çalışma Saatleri -->
                    <div>
                        <label for="working_hours" class="block text-sm font-semibold text-gray-700 mb-1">
                            Çalışma Saatleri
                        </label>
                        <div class="relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="working_hours" 
                                id="working_hours" 
                                value="{{ old('working_hours', $provider->working_hours ?? '') }}" 
                                placeholder="Örn: Hafta içi 08:30 - 18:00, Cumartesi 09:00 - 14:00" 
                                class="block w-full pl-9 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 @error('working_hours') border-red-300 @enderror"
                            >
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Müşterilerin size hangi saatler arasında ulaşabileceğini belirtin.</p>
                        @error('working_hours')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Biyografi (Textarea) -->
                    <div>
                        <label for="bio" class="block text-sm font-semibold text-gray-700 mb-1">
                            Hakkınızda & Biyografi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative rounded-lg shadow-sm">
                            <textarea 
                                name="bio" 
                                id="bio" 
                                rows="5" 
                                placeholder="Tecrübeniz, sunduğunuz hizmetler, garanti koşullarınız ve referanslarınız hakkında detaylı bilgi verin..." 
                                class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3.5 leading-relaxed @error('bio') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                required
                            >{{ old('bio', $provider->bio ?? '') }}</textarea>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Müşterilerin sizi tercih etmesini sağlayacak detaylı bir tanıtım yazısı yazın.</p>
                        @error('bio')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Butonlar -->
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                        <a 
                            href="{{ route('provider.dashboard') }}" 
                            class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition shadow-sm"
                        >
                            İptal
                        </a>
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            İş Bilgilerini Kaydet
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>

