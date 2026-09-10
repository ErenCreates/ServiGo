<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Usta Paneli') }}
            </h2>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('provider.appointments.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-sm transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Randevularım
                </a>
                <a href="{{ route('provider.profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-sm transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    İş Bilgilerimi Düzenle
                </a>
            </div>
        </div>
    </x-slot>

    <div 
        class="py-10" 
        x-data="{ 
            acceptModalOpen: false, 
            isSubmitting: false,
            selectedRequest: { id: null, customerName: '', description: '', appointmentDate: '{{ now()->addDay()->format('Y-m-d\T10:00') }}', note: '' },
            requestsStatus: { @foreach($requests as $req) '{{ $req->id }}': '{{ $req->status }}', @endforeach },
            toast: { show: false, message: '', type: 'success' },
            showToast(msg, type = 'success') {
                this.toast.message = msg;
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 4000);
            },
            async submitAccept() {
                if (this.isSubmitting) return;
                const reqId = this.selectedRequest.id;
                if (!reqId) return;

                this.isSubmitting = true;
                try {
                    const token = document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content');
                    const response = await fetch('/provider/requests/' + reqId + '/accept', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token || '',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            appointment_date: this.selectedRequest.appointmentDate,
                            note: this.selectedRequest.note
                        })
                    });
                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.requestsStatus[reqId] = 'accepted';
                        this.acceptModalOpen = false;
                        this.showToast(data.message || 'Talep kabul edildi ve randevu oluşturuldu!', 'success');
                    } else {
                        this.showToast(data.message || 'İşlem gerçekleştirilemedi.', 'error');
                    }
                } catch (err) {
                    console.error('AJAX error, submitting via standard form:', err);
                    document.getElementById('accept-form-' + reqId)?.submit();
                } finally {
                    this.isSubmitting = false;
                }
            },
            async rejectRequest(reqId) {
                if (!confirm('Bu talebi reddetmek istediğinizden emin misiniz?')) return;
                this.isSubmitting = true;
                try {
                    const token = document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content');
                    const response = await fetch('/provider/requests/' + reqId + '/reject', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token || '',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.requestsStatus[reqId] = 'rejected';
                        this.showToast(data.message || 'Talep reddedildi.', 'info');
                    } else {
                        this.showToast(data.message || 'İşlem gerçekleştirilemedi.', 'error');
                    }
                } catch (err) {
                    console.error('AJAX error, submitting via standard form:', err);
                    document.getElementById('reject-form-' + reqId)?.submit();
                } finally {
                    this.isSubmitting = false;
                }
            }
        }"
    >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Canlı AJAX Toast Bildirimi -->
            <div 
                x-show="toast.show" 
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                class="fixed bottom-6 right-6 z-50 p-4 rounded-2xl shadow-2xl flex items-center space-x-3 text-sm font-bold border"
                :class="toast.type === 'success' ? 'bg-emerald-600 text-white border-emerald-500' : (toast.type === 'info' ? 'bg-indigo-600 text-white border-indigo-500' : 'bg-rose-600 text-white border-rose-500')"
                style="display: none;"
            >
                <template x-if="toast.type === 'success'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </template>
                <template x-if="toast.type !== 'success'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </template>
                <span x-text="toast.message"></span>
            </div>

            <!-- Standart Flash Mesajları -->
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center shadow-sm">
                    <svg class="w-5 h-5 text-emerald-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center shadow-sm">
                    <svg class="w-5 h-5 text-rose-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
            @endif
            
            <!-- Uyarı Mesajı (Profil yoksa) -->
            @if(!auth()->user()->serviceProvider || empty(auth()->user()->serviceProvider->category_id))
                <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex flex-col sm:flex-row sm:items-center sm:justify-between shadow-sm gap-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-amber-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium">Müşterilerden gelen hizmet taleplerini listeleyebilmek için lütfen usta profil bilgilerinizi tamamlayın.</span>
                    </div>
                    <a href="{{ route('provider.profile.edit') }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition whitespace-nowrap shadow-sm">
                        İş Bilgilerimi Düzenle &rarr;
                    </a>
                </div>
            @else
                <!-- Profil Özet Kartı (Responsive) -->
                <div class="p-5 bg-white rounded-3xl border border-gray-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 font-medium">İşletme / İsim:</span>
                            <span class="font-bold text-gray-900 ml-1">{{ auth()->user()->serviceProvider->company_name ?? auth()->user()->name }}</span>
                        </div>
                        <div class="hidden sm:inline text-gray-300">|</div>
                        <div>
                            <span class="text-gray-500 font-medium">Kategori:</span>
                            <span class="font-semibold text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full text-xs ml-1">{{ auth()->user()->serviceProvider->category->name ?? 'Seçilmedi' }}</span>
                        </div>
                        @if(auth()->user()->serviceProvider->working_hours)
                            <div class="hidden sm:inline text-gray-300">|</div>
                            <div>
                                <span class="text-gray-500 font-medium">Çalışma Saatleri:</span>
                                <span class="text-gray-700 ml-1">{{ auth()->user()->serviceProvider->working_hours }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('provider.appointments.index') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition whitespace-nowrap">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Randevularım
                        </a>
                        <a href="{{ route('provider.profile.edit') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Düzenle
                        </a>
                    </div>
                </div>
            @endif

            <!-- Talepler Tablosu (Tam Responsive) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-200">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Gelen Hizmet Talepleri</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Müşterilerden gelen talepleri inceleyip anlık randevuya dönüştürün.</p>
                        </div>
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1 rounded-full self-start sm:self-auto">
                            {{ $requests->count() }} Talep
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Müşteri</th>
                                    <th class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kategori</th>
                                    <th class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Talep Açıklaması</th>
                                    <th class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tarih</th>
                                    <th class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Durum</th>
                                    <th class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">İşlem</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($requests as $request)
                                <tr class="hover:bg-gray-50/50 transition" :id="'request-row-' + {{ $request->id }}">
                                    <!-- Müşteri -->
                                    <td class="px-5 py-4 text-sm font-bold text-gray-900 whitespace-nowrap">
                                        {{ $request->customer->name }}
                                        <div class="text-xs text-gray-400 font-normal">{{ $request->customer->email }}</div>
                                    </td>

                                    <!-- Kategori -->
                                    <td class="px-5 py-4 text-xs font-semibold text-gray-700 whitespace-nowrap">
                                        <span class="px-2.5 py-1 bg-gray-100 rounded-full">{{ $request->category->name }}</span>
                                    </td>

                                    <!-- Talep Açıklaması -->
                                    <td class="px-5 py-4 text-xs text-gray-600 max-w-xs">
                                        {{ Str::limit($request->description, 60) }}
                                    </td>

                                    <!-- Tarih -->
                                    <td class="px-5 py-4 text-xs text-gray-500 whitespace-nowrap">
                                        {{ $request->created_at->format('d.m.Y H:i') }}
                                    </td>

                                    <!-- Dinamik Durum Rozeti -->
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <template x-if="requestsStatus[{{ $request->id }}] === 'pending'">
                                            <span class="px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200 rounded-full font-bold text-xs inline-flex items-center">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                                Bekliyor
                                            </span>
                                        </template>
                                        <template x-if="requestsStatus[{{ $request->id }}] === 'accepted'">
                                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-full font-bold text-xs inline-flex items-center">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                Kabul Edildi
                                            </span>
                                        </template>
                                        <template x-if="requestsStatus[{{ $request->id }}] === 'completed'">
                                            <span class="px-2.5 py-1 bg-blue-50 text-blue-800 border border-blue-200 rounded-full font-bold text-xs inline-flex items-center">
                                                Tamamlandı
                                            </span>
                                        </template>
                                        <template x-if="requestsStatus[{{ $request->id }}] === 'rejected'">
                                            <span class="px-2.5 py-1 bg-rose-50 text-rose-800 border border-rose-200 rounded-full font-bold text-xs inline-flex items-center">
                                                Reddedildi
                                            </span>
                                        </template>
                                    </td>

                                    <!-- Dinamik İşlem Butonları (AJAX & Fallback) -->
                                    <td class="px-5 py-4 text-right text-xs whitespace-nowrap">
                                        <template x-if="requestsStatus[{{ $request->id }}] === 'pending'">
                                            <div class="flex items-center justify-end space-x-2">
                                                <!-- Randevu ile Kabul Et Butonu -->
                                                <button 
                                                    type="button"
                                                    @click="selectedRequest = { id: {{ $request->id }}, customerName: '{{ addslashes($request->customer->name) }}', description: '{{ addslashes(Str::limit($request->description, 60)) }}', appointmentDate: '{{ now()->addDay()->format('Y-m-d\T10:00') }}', note: '' }; acceptModalOpen = true"
                                                    class="bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold py-1.5 px-3 rounded-xl text-xs transition shadow-sm flex items-center cursor-pointer"
                                                >
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    Kabul Et & Randevu Belirle
                                                </button>

                                                <!-- Reddet Butonu (AJAX) -->
                                                <button 
                                                    type="button"
                                                    @click="rejectRequest({{ $request->id }})"
                                                    class="bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 font-semibold py-1.5 px-3 rounded-xl text-xs transition cursor-pointer"
                                                >
                                                    Reddet
                                                </button>

                                                <!-- Fallback Form (Non-JS) -->
                                                <form id="reject-form-{{ $request->id }}" action="{{ route('provider.request.reject', $request->id) }}" method="POST" class="hidden">
                                                    @csrf
                                                </form>
                                            </div>
                                        </template>

                                        <template x-if="requestsStatus[{{ $request->id }}] === 'accepted'">
                                            <a href="{{ route('provider.appointments.index') }}" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-800 transition">
                                                Randevuya Git &rarr;
                                            </a>
                                        </template>

                                        <template x-if="requestsStatus[{{ $request->id }}] === 'rejected' || requestsStatus[{{ $request->id }}] === 'completed'">
                                            <span class="text-gray-400 text-xs italic">İşlem Tamamlandı</span>
                                        </template>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center text-gray-500 text-sm italic">
                                        Henüz hiç hizmet talebiniz bulunmuyor.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Randevu Oluşturarak Kabul Etme Modalı (AJAX Destekli) -->
        <div 
            x-show="acceptModalOpen" 
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            style="display: none;"
        >
            <div 
                @click.away="acceptModalOpen = false" 
                class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6"
            >
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Talebi Kabul Et & Randevu Belirle</h3>
                        <p class="text-xs text-gray-500 mt-0.5" x-text="'Müşteri: ' + selectedRequest.customerName"></p>
                    </div>
                    <button @click="acceptModalOpen = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                </div>

                <form @submit.prevent="submitAccept()" :id="'accept-form-' + selectedRequest.id" :action="'/provider/requests/' + selectedRequest.id + '/accept'" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="modal_appointment_date" class="block text-sm font-semibold text-gray-700 mb-1">
                            Randevu Tarihi ve Saati <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="datetime-local" 
                            name="appointment_date" 
                            id="modal_appointment_date" 
                            x-model="selectedRequest.appointmentDate"
                            required 
                            class="w-full rounded-2xl border-gray-300 focus:border-indigo-600 focus:ring-indigo-100 text-sm py-2.5"
                        >
                        <p class="text-xs text-gray-400 mt-1">Müşteriye hizmet vermek için uygun olduğunuz randevu tarihini seçin.</p>
                    </div>

                    <div>
                        <label for="modal_note" class="block text-sm font-semibold text-gray-700 mb-1">Randevu Notu / Açıklama</label>
                        <textarea 
                            name="note" 
                            id="modal_note" 
                            rows="3" 
                            x-model="selectedRequest.note"
                            placeholder="Örn: Saat 10:00'da adreste olacağım. Lütfen sigortaları kapalı tutunuz." 
                            class="w-full rounded-2xl border-gray-300 focus:border-indigo-600 focus:ring-indigo-100 text-sm py-2"
                        ></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="acceptModalOpen = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-xs transition cursor-pointer">Vazgeç</button>
                        <button 
                            type="submit" 
                            :disabled="isSubmitting"
                            class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span x-show="!isSubmitting">Randevuyu Onayla & Kaydet</span>
                            <span x-show="isSubmitting" style="display: none;">Kaydediliyor...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>