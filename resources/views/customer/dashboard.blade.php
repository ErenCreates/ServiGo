<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hizmet Al / Usta Bul') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Hizmet Kategorileri -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Hizmet Kategorileri</h3>
                <div class="flex flex-wrap gap-3">
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-full text-sm font-semibold shadow-sm hover:bg-indigo-500 transition">Tümü</button>
                    @foreach($categories as $category)
                        <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-full text-sm font-semibold shadow-sm hover:bg-gray-50 transition">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Usta Kartları -->
            <h3 class="text-lg font-medium text-gray-900 mb-4">Öne Çıkan Ustalar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($providers as $provider)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2.5 py-0.5 bg-green-100 text-green-800 text-xs font-bold rounded-full uppercase tracking-wide">
                                    {{ $provider->category->name }}
                                </span>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $provider->company_name }}</h4>
                            <p class="text-sm text-gray-600 mb-6 line-clamp-3">
                                {{ $provider->bio }}
                            </p>
                            <div class="flex items-center justify-between mt-auto">
                                <div class="text-sm text-gray-500">
                                    <span class="font-medium text-gray-900">{{ $provider->user->name }}</span>
                                </div>
                                <a href="{{ route('customer.provider.show', $provider->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                                    İncele
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white p-12 text-center rounded-lg shadow">
                        <p class="text-gray-500 italic text-lg">Henüz sistemde kayıtlı usta bulunmuyor.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
