<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('İş Profilini Düzenle') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Hizmet kategorinizi, çalışma saatlerinizi, biyografi ve harita konumunuzu güncelleyin.
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

    <!-- Leaflet.js CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

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

            <div class="bg-white shadow-sm sm:rounded-3xl border border-gray-200 overflow-hidden">
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

                    <!-- Gizli Koordinat Alanları -->
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $provider->latitude ?? 41.0082) }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $provider->longitude ?? 28.9784) }}">

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
                                class="block w-full rounded-2xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3.5"
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
                                class="block w-full rounded-2xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3.5 bg-white @error('category_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
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
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
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
                                class="block w-full pl-10 rounded-2xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 @error('working_hours') border-red-300 @enderror"
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
                                rows="4" 
                                placeholder="Tecrübeniz, sunduğunuz hizmetler, garanti koşullarınız ve referanslarınız hakkında detaylı bilgi verin..." 
                                class="block w-full rounded-2xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5 px-3.5 leading-relaxed @error('bio') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                required
                            >{{ old('bio', $provider->bio ?? '') }}</textarea>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Müşterilerin sizi tercih etmesini sağlayacak detaylı bir tanıtım yazısı yazın.</p>
                        @error('bio')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- İnteraktif Harita Konum Seçici (Leaflet.js + OpenStreetMap) -->
                    <div class="p-5 sm:p-6 bg-slate-50 rounded-3xl border border-slate-200 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="w-7 h-7 rounded-xl bg-indigo-600 text-white flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </span>
                                    <h4 class="text-sm font-extrabold text-gray-900 uppercase tracking-wider">
                                        İş / Hizmet Konumunuzu Belirleyin
                                    </h4>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    Harita üzerinde dükkanınızın veya hizmet verdiğiniz bölgenin üzerine <strong>tıklayın</strong> ya da pini sürükleyin.
                                </p>
                            </div>

                            <button 
                                type="button" 
                                id="btn-current-location"
                                class="inline-flex items-center px-3.5 py-2 bg-white hover:bg-gray-100 text-indigo-700 border border-indigo-200 rounded-xl text-xs font-bold shadow-sm transition whitespace-nowrap self-start sm:self-auto cursor-pointer"
                            >
                                <svg class="w-3.5 h-3.5 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
                                </svg>
                                Konumumu Otomatik Bul
                            </button>
                        </div>

                        <!-- Seçilen Koordinat Bilgi Rozeti -->
                        <div class="flex items-center justify-between p-3 bg-white rounded-2xl border border-gray-200 text-xs font-semibold text-gray-700 shadow-sm">
                            <span class="flex items-center text-emerald-700 font-bold">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                                Seçilen Konum:
                            </span>
                            <span id="coords-display" class="font-mono text-gray-800 bg-gray-100 px-3 py-1 rounded-lg">
                                Enlem: {{ number_format((float) old('latitude', $provider->latitude ?? 41.0082), 6) }}, Boylam: {{ number_format((float) old('longitude', $provider->longitude ?? 28.9784), 6) }}
                            </span>
                        </div>

                        <!-- Harita Container -->
                        <div id="map-picker" class="w-full h-80 rounded-2xl border border-gray-300 shadow-inner z-0"></div>
                    </div>

                    <!-- Butonlar -->
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                        <a 
                            href="{{ route('provider.dashboard') }}" 
                            class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition shadow-sm"
                        >
                            İptal
                        </a>
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold text-sm rounded-xl shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition"
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

    <!-- Leaflet İnteraktif Harita Seçici Scripti -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapPicker = document.getElementById('map-picker');
            if (!mapPicker || typeof L === 'undefined') return;

            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const coordsDisplay = document.getElementById('coords-display');
            const btnCurrentLocation = document.getElementById('btn-current-location');

            let currentLat = parseFloat(latInput.value) || 41.0082;
            let currentLng = parseFloat(lngInput.value) || 28.9784;

            // Haritayı başlat
            const map = L.map('map-picker', {
                scrollWheelZoom: true
            }).setView([currentLat, currentLng], 12);

            // OpenStreetMap ücretsiz karo katmanı
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>'
            }).addTo(map);

            // Sürüklenebilir konum pini (Marker)
            const marker = L.marker([currentLat, currentLng], {
                draggable: true
            }).addTo(map);

            function updatePosition(lat, lng, zoomTo = false) {
                const roundedLat = parseFloat(lat).toFixed(6);
                const roundedLng = parseFloat(lng).toFixed(6);

                latInput.value = roundedLat;
                lngInput.value = roundedLng;
                coordsDisplay.textContent = `Enlem: ${roundedLat}, Boylam: ${roundedLng}`;

                marker.setLatLng([lat, lng]);
                marker.bindPopup(`
                    <div style="font-family: inherit; font-size: 12px; font-weight: 700; color: #111827; padding: 2px;">
                        📍 Seçilen Konum<br>
                        <span style="font-size: 10px; color: #6b7280; font-weight: normal;">Enlem: ${roundedLat}, Boylam: ${roundedLng}</span>
                    </div>
                `).openPopup();

                if (zoomTo) {
                    map.setView([lat, lng], 14);
                }
            }

            // İlk popup'ı aç
            updatePosition(currentLat, currentLng);

            // Haritada bir yere tıklandığında konumu güncelle
            map.on('click', function (e) {
                updatePosition(e.latlng.lat, e.latlng.lng);
            });

            // Pin sürüklendiğinde konumu güncelle
            marker.on('dragend', function (e) {
                const position = marker.getLatLng();
                updatePosition(position.lat, position.lng);
            });

            // Otomatik konum bulma butonu
            if (btnCurrentLocation && navigator.geolocation) {
                btnCurrentLocation.addEventListener('click', function () {
                    btnCurrentLocation.disabled = true;
                    btnCurrentLocation.innerHTML = `<span class="animate-spin mr-1.5">🔄</span> Konum alınıyor...`;

                    navigator.geolocation.getCurrentPosition(
                        function (pos) {
                            const userLat = pos.coords.latitude;
                            const userLng = pos.coords.longitude;
                            updatePosition(userLat, userLng, true);
                            btnCurrentLocation.disabled = false;
                            btnCurrentLocation.innerHTML = `📍 Konum Güncellendi!`;
                            setTimeout(() => {
                                btnCurrentLocation.innerHTML = `
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
                                    </svg>
                                    Konumumu Otomatik Bul
                                `;
                            }, 3000);
                        },
                        function (err) {
                            alert('Tarayıcınızdan konum bilgisi alınamadı. Lütfen harita üzerinden tıklayarak seçiniz.');
                            btnCurrentLocation.disabled = false;
                            btnCurrentLocation.innerHTML = `📍 Konumumu Otomatik Bul`;
                        }
                    );
                });
            }
        });
    </script>
</x-app-layout>
