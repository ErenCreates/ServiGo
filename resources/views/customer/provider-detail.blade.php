<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $provider->company_name }} - Profil Detayı
            </h2>
            <a href="{{ route('customer.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                &larr; Geri Dön
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200">
                <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Usta Hakkında Bilgiler</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Hizmet kalitesi ve detaylı biyografi.</p>
                </div>
                <div class="px-4 py-6 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Şirket / Marka Adı</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-bold">{{ $provider->company_name }}</dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50">
                            <dt class="text-sm font-medium text-gray-500">Usta Ad Soyad</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $provider->user->name }}</dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Hizmet Kategorisi</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-bold uppercase">
                                    {{ $provider->category->name }}
                                </span>
                            </dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50">
                            <dt class="text-sm font-medium text-gray-500">E-posta</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $provider->user->email }}</dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Biyografi</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 leading-relaxed italic">
                                "{{ $provider->bio }}"
                            </dd>
                        </div>
                    </dl>
                </div>
                <div class="px-6 py-5 bg-gray-50 text-right border-t border-gray-200">
                    <button class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-bold text-white uppercase tracking-widest hover:bg-indigo-500 transition shadow-md">
                        Hizmet Talebi Oluştur
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>