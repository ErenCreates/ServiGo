<x-admin-layout>
    <x-slot name="header">
        Hizmet Kategorisi Yönetimi
    </x-slot>

    <div class="space-y-6" x-data="{ createModalOpen: false, editModalOpen: false, editCategory: { id: null, name: '', description: '', is_active: true } }">
        
        <!-- Üst Başlık & Arama Çubuğu -->
        <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-wrap items-center gap-3 flex-grow">
                <div class="relative min-w-[240px] flex-grow max-w-md">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Kategori adı veya açıklama ara..." 
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
                    <option value="active" @selected(request('status') === 'active')>Sadece Aktifler</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Sadece Pasifler</option>
                </select>

                <button type="submit" class="px-4 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-bold transition">
                    Filtrele
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.categories.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Temizle
                    </a>
                @endif
            </form>

            <button 
                @click="createModalOpen = true" 
                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center whitespace-nowrap cursor-pointer"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Yeni Kategori Ekle
            </button>
        </div>

        <!-- Kategoriler Tablosu -->
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kategori Adı</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Açıklama</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Usta Sayısı</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Durum</th>
                            <th class="px-6 py-3.5 border-b border-gray-200 bg-gray-50 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2.5 h-2.5 rounded-full {{ $category->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}"></div>
                                        <span>{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-600 max-w-md">
                                    {{ $category->description ?: 'Açıklama belirtilmemiş.' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                        {{ $category->service_providers_count }} Usta
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    @if($category->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            Pasif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-xs whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2">
                                        
                                        <!-- Aktif / Pasif Butonu -->
                                        <form action="{{ route('admin.categories.toggle', $category->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit" 
                                                title="{{ $category->is_active ? 'Pasife Al' : 'Aktifleştir' }}"
                                                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold {{ $category->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }} transition"
                                            >
                                                {{ $category->is_active ? 'Pasife Al' : 'Aktifleştir' }}
                                            </button>
                                        </form>

                                        <!-- Düzenle Butonu -->
                                        <button 
                                            @click="editCategory = { id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', description: '{{ addslashes($category->description ?? '') }}', is_active: {{ $category->is_active ? 'true' : 'false' }} }; editModalOpen = true" 
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition"
                                        >
                                            Düzenle
                                        </button>

                                        <!-- Sil Butonu -->
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Bu kategoriyi silmek istediğinizden emin misiniz?');">
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
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-sm italic">
                                    Kayıtlı kategori bulunamadı.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Yeni Kategori Ekleme Modalı -->
        <div 
            x-show="createModalOpen" 
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            style="display: none;"
        >
            <div 
                @click.away="createModalOpen = false" 
                class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6"
            >
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <h3 class="text-lg font-bold text-gray-900">Yeni Hizmet Kategorisi Ekle</h3>
                    <button @click="createModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Kategori Adı <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required placeholder="Örn: Klima & İklimlendirme" class="w-full rounded-xl border-gray-300 focus:border-indigo-600 focus:ring-indigo-100 text-sm py-2.5">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Açıklama</label>
                        <textarea name="description" id="description" rows="3" placeholder="Kategori kapsamındaki hizmetler..." class="w-full rounded-xl border-gray-300 focus:border-indigo-600 focus:ring-indigo-100 text-sm py-2"></textarea>
                    </div>

                    <div class="flex items-center">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700 font-medium">Bu kategori aktif olarak yayınlansın</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-xl text-xs">İptal</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-md">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kategori Düzenleme Modalı -->
        <div 
            x-show="editModalOpen" 
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            style="display: none;"
        >
            <div 
                @click.away="editModalOpen = false" 
                class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6"
            >
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <h3 class="text-lg font-bold text-gray-900">Kategoriyi Düzenle</h3>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form :action="'/admin/kategoriler/' + editCategory.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori Adı <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="editCategory.name" required class="w-full rounded-xl border-gray-300 focus:border-indigo-600 focus:ring-indigo-100 text-sm py-2.5">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Açıklama</label>
                        <textarea name="description" x-model="editCategory.description" rows="3" class="w-full rounded-xl border-gray-300 focus:border-indigo-600 focus:ring-indigo-100 text-sm py-2"></textarea>
                    </div>

                    <div class="flex items-center">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="editCategory.is_active" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700 font-medium">Bu kategori aktif olsun</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-xl text-xs">İptal</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs shadow-md">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-admin-layout>

