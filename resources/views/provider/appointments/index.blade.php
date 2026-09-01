<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Usta Randevuları') }}
            </h2>
            <a href="{{ route('provider.dashboard') }}" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                &larr; Gelen Taleplere Dön
            </a>
        </div>
    </x-slot>

    <div 
        class="py-10" 
        x-data="{ 
            rescheduleModalOpen: false, 
            selectedAppointment: { id: null, date: '', note: '' },
            aptStatus: { @foreach($appointments as $apt) '{{ $apt->id }}': '{{ $apt->status }}', @endforeach },
            loading: {},
            toast: { show: false, message: '', type: 'success' },
            showToast(msg, type = 'success') {
                this.toast.message = msg;
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 4000);
            },
            async completeAppointment(id) {
                if (!confirm('Bu randevuyu tamamlandı olarak işaretlemek istediğinizden emin misiniz?')) return;
                this.loading[id] = true;
                try {
                    const token = document.querySelector('meta[name=\'csrf-token\']').getAttribute('content');
                    const response = await fetch('/usta/randevular/' + id + '/tamamla', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ _method: 'PATCH' })
                    });
                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.aptStatus[id] = 'completed';
                        this.showToast(data.message || 'Randevu tamamlandı olarak işaretlendi.', 'success');
                    } else {
                        this.showToast(data.message || 'İşlem gerçekleştirilemedi.', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    document.getElementById('complete-form-' + id)?.submit();
                } finally {
                    this.loading[id] = false;
                }
            },
            async cancelAppointment(id) {
                if (!confirm('Bu randevuyu iptal etmek istediğinizden emin misiniz?')) return;
                this.loading[id] = true;
                try {
                    const token = document.querySelector('meta[name=\'csrf-token\']').getAttribute('content');
                    const response = await fetch('/usta/randevular/' + id + '/iptal', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ _method: 'PATCH' })
                    });
                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.aptStatus[id] = 'cancelled';
                        this.showToast(data.message || 'Randevu iptal edildi.', 'info');
                    } else {
                        this.showToast(data.message || 'İşlem gerçekleştirilemedi.', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    document.getElementById('cancel-form-' + id)?.submit();
                } finally {
                    this.loading[id] = false;
                }
            },
            async submitReschedule() {
                const id = this.selectedAppointment.id;
                this.loading[id] = true;
                try {
                    const token = document.querySelector('meta[name=\'csrf-token\']').getAttribute('content');
                    const response = await fetch('/usta/randevular/' + id + '/ertele', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'PATCH',
                            appointment_date: this.selectedAppointment.date,
                            note: this.selectedAppointment.note
                        })
                    });
                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.aptStatus[id] = 'scheduled';
                        this.rescheduleModalOpen = false;
                        this.showToast(data.message || 'Randevu tarihi başarıyla güncellendi.', 'success');
                    } else {
                        this.showToast(data.message || 'İşlem gerçekleştirilemedi.', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    document.getElementById('reschedule-form-' + id)?.submit();
                } finally {
                    this.loading[id] = false;
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

            <!-- Sayaç / Filtre Çipleri -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <a href="{{ route('provider.appointments.index') }}" class="p-5 rounded-3xl border transition {{ !request('status') || request('status') === 'all' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-200' }}">
                    <div class="text-xs font-bold uppercase tracking-wider opacity-80">Tüm Randevular</div>
                    <div class="text-2xl font-black mt-1">{{ $stats['total'] }}</div>
                </a>

                <a href="{{ route('provider.appointments.index', ['status' => 'scheduled']) }}" class="p-5 rounded-3xl border transition {{ request('status') === 'scheduled' ? 'bg-amber-500 text-white border-amber-500 shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-200' }}">
                    <div class="text-xs font-bold uppercase tracking-wider opacity-80">Planlananlar</div>
                    <div class="text-2xl font-black mt-1">{{ $stats['scheduled'] }}</div>
                </a>

                <a href="{{ route('provider.appointments.index', ['status' => 'completed']) }}" class="p-5 rounded-3xl border transition {{ request('status') === 'completed' ? 'bg-emerald-600 text-white border-emerald-600 shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-200' }}">
                    <div class="text-xs font-bold uppercase tracking-wider opacity-80">Tamamlananlar</div>
                    <div class="text-2xl font-black mt-1">{{ $stats['completed'] }}</div>
                </a>

                <a href="{{ route('provider.appointments.index', ['status' => 'cancelled']) }}" class="p-5 rounded-3xl border transition {{ request('status') === 'cancelled' ? 'bg-rose-600 text-white border-rose-600 shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-200' }}">
                    <div class="text-xs font-bold uppercase tracking-wider opacity-80">İptal Edilenler</div>
                    <div class="text-2xl font-black mt-1">{{ $stats['cancelled'] }}</div>
                </a>
            </div>

            <!-- Randevular Listesi -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-3xl border border-gray-200">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Randevu Listesi</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Planlanmış işlerinizi takip edin, tamamlandı olarak işaretleyin veya erteleyin.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Randevu Tarihi & Saati</th>
                                    <th class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Müşteri</th>
                                    <th class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Hizmet / Açıklama</th>
                                    <th class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Not / Adres</th>
                                    <th class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Durum</th>
                                    <th class="px-5 py-3.5 border-b border-gray-200 bg-gray-50 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($appointments as $apt)
                                    <tr class="hover:bg-gray-50/50 transition" :id="'apt-row-' + {{ $apt->id }}">
                                        <!-- Tarih & Saat -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex flex-col items-center justify-center font-bold">
                                                    <span class="text-[10px] uppercase leading-none">{{ $apt->appointment_date->format('M') }}</span>
                                                    <span class="text-sm font-black leading-none mt-0.5">{{ $apt->appointment_date->format('d') }}</span>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-sm text-gray-900">{{ $apt->appointment_date->format('d.m.Y') }}</div>
                                                    <div class="text-xs text-indigo-600 font-bold">Saat: {{ $apt->appointment_date->format('H:i') }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Müşteri -->
                                        <td class="px-5 py-4">
                                            <div class="font-bold text-sm text-gray-900">{{ $apt->customer->name }}</div>
                                            <div class="text-xs text-gray-500 font-normal">{{ $apt->customer->email }}</div>
                                        </td>

                                        <!-- Hizmet & Açıklama -->
                                        <td class="px-5 py-4 text-xs">
                                            @if($apt->serviceRequest && $apt->serviceRequest->category)
                                                <span class="inline-block px-2.5 py-0.5 rounded-md bg-gray-100 text-gray-700 font-semibold mb-1">
                                                    {{ $apt->serviceRequest->category->name }}
                                                </span>
                                            @endif
                                            <div class="text-gray-600 max-w-xs">{{ Str::limit($apt->serviceRequest->description ?? 'Açıklama yok', 50) }}</div>
                                        </td>

                                        <!-- Not -->
                                        <td class="px-5 py-4 text-xs text-gray-600 max-w-xs">
                                            {{ $apt->note ?: '-' }}
                                        </td>

                                        <!-- Durum (Dinamik) -->
                                        <td class="px-5 py-4 whitespace-nowrap">
                                            <template x-if="aptStatus[{{ $apt->id }}] === 'scheduled'">
                                                <span class="px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200 rounded-full font-bold text-xs inline-flex items-center">
                                                    Planlandı
                                                </span>
                                            </template>
                                            <template x-if="aptStatus[{{ $apt->id }}] === 'completed'">
                                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-full font-bold text-xs inline-flex items-center">
                                                    Tamamlandı
                                                </span>
                                            </template>
                                            <template x-if="aptStatus[{{ $apt->id }}] === 'cancelled'">
                                                <span class="px-2.5 py-1 bg-rose-50 text-rose-800 border border-rose-200 rounded-full font-bold text-xs inline-flex items-center">
                                                    İptal Edildi
                                                </span>
                                            </template>
                                        </td>

                                        <!-- İşlemler (AJAX) -->
                                        <td class="px-5 py-4 text-right text-xs whitespace-nowrap">
                                            <template x-if="aptStatus[{{ $apt->id }}] === 'scheduled'">
                                                <div class="flex items-center justify-end space-x-2">
                                                    
                                                    <!-- Tamamlandı Olarak İşaretle (AJAX) -->
                                                    <button 
                                                        type="button"
                                                        :disabled="loading[{{ $apt->id }}]"
                                                        @click="completeAppointment({{ $apt->id }})"
                                                        title="İş tamamlandı olarak kaydet"
                                                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold rounded-xl transition shadow-sm cursor-pointer disabled:opacity-50"
                                                    >
                                                        Tamamlandı
                                                    </button>
                                                    <form id="complete-form-{{ $apt->id }}" action="{{ route('provider.appointments.complete', $apt->id) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('PATCH')
                                                    </form>

                                                    <!-- Ertele / Tarih Değiştir -->
                                                    <button 
                                                        type="button"
                                                        :disabled="loading[{{ $apt->id }}]"
                                                        @click="selectedAppointment = { id: {{ $apt->id }}, date: '{{ $apt->appointment_date->format('Y-m-d\TH:i') }}', note: '{{ addslashes($apt->note ?? '') }}' }; rescheduleModalOpen = true"
                                                        class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition cursor-pointer disabled:opacity-50"
                                                    >
                                                        Ertele
                                                    </button>

                                                    <!-- İptal Et (AJAX) -->
                                                    <button 
                                                        type="button"
                                                        :disabled="loading[{{ $apt->id }}]"
                                                        @click="cancelAppointment({{ $apt->id }})"
                                                        class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold rounded-xl transition cursor-pointer disabled:opacity-50"
                                                    >
                                                        İptal
                                                    </button>
                                                    <form id="cancel-form-{{ $apt->id }}" action="{{ route('provider.appointments.cancel', $apt->id) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('PATCH')
                                                    </form>
                                                </div>
                                            </template>

                                            <template x-if="aptStatus[{{ $apt->id }}] !== 'scheduled'">
                                                <span class="text-gray-400 text-xs italic">Kayıt Arşivlendi</span>
                                            </template>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-12 text-center text-gray-500 text-sm italic">
                                            Kayıtlı randevunuz bulunmuyor.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Sayfalama -->
                    @if($appointments->hasPages())
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            {{ $appointments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Randevu Erteleme Modalı (AJAX Destekli) -->
        <div 
            x-show="rescheduleModalOpen" 
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            style="display: none;"
        >
            <div 
                @click.away="rescheduleModalOpen = false" 
                class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6"
            >
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <h3 class="text-lg font-bold text-gray-900">Randevu Tarihini Yeniden Belirle</h3>
                    <button @click="rescheduleModalOpen = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                </div>

                <form @submit.prevent="submitReschedule()" :id="'reschedule-form-' + selectedAppointment.id" :action="'/usta/randevular/' + selectedAppointment.id + '/ertele'" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="reschedule_date" class="block text-sm font-semibold text-gray-700 mb-1">Yeni Tarih ve Saat <span class="text-red-500">*</span></label>
                        <input 
                            type="datetime-local" 
                            name="appointment_date" 
                            id="reschedule_date" 
                            x-model="selectedAppointment.date"
                            min="{{ now()->format('Y-m-d\TH:i') }}"
                            required 
                            class="w-full rounded-2xl border-gray-300 focus:border-indigo-600 focus:ring-indigo-100 text-sm py-2.5"
                        >
                    </div>

                    <div>
                        <label for="reschedule_note" class="block text-sm font-semibold text-gray-700 mb-1">Erteleme Notu / Açıklama</label>
                        <textarea 
                            name="note" 
                            id="reschedule_note" 
                            rows="3" 
                            x-model="selectedAppointment.note"
                            class="w-full rounded-2xl border-gray-300 focus:border-indigo-600 focus:ring-indigo-100 text-sm py-2"
                        ></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="rescheduleModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-xl text-xs">Vazgeç</button>
                        <button 
                            type="submit" 
                            :disabled="loading[selectedAppointment.id]"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold rounded-xl text-xs shadow-md disabled:opacity-50"
                        >
                            <span x-show="!loading[selectedAppointment.id]">Güncelle & Kaydet</span>
                            <span x-show="loading[selectedAppointment.id]">Kaydediliyor...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
