<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Usta Paneli — Profil Yönetimi
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Profil Bilgileri</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Müşterilerin sizi doğru bulabilmesi için profilinizi eksiksiz doldurun.
                    </p>
                </div>

                <form method="POST" action="{{ route('provider.dashboard.update') }}" class="px-8 py-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Şirket Adı
                        </label>
                        <input
                            type="text"
                            id="company_name"
                            name="company_name"
                            value="{{ old('company_name', $provider->company_name ?? '') }}"
                            placeholder="Örn: Yılmaz Elektrik Tesisat"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm
                                @error('company_name') border-red-400 @enderror"
                        >
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Hizmet Kategorisi
                        </label>
                        <select
                            id="category_id"
                            name="category_id"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm
                                @error('category_id') border-red-400 @enderror"
                        >
                            <option value="" disabled {{ old('category_id', $provider->category_id ?? '') ? '' : 'selected' }}>
                                Bir kategori seçin
                            </option>
                            @if(isset($categories))
                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->id }}"
                                        {{ (int) old('category_id', $provider->category_id ?? 0) === $category->id ? 'selected' : '' }}
                                    >
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">
                            Biyografi
                        </label>
                        <textarea
                            id="bio"
                            name="bio"
                            rows="5"
                            placeholder="Kendinizi ve verdiğiniz hizmetleri kısaca tanıtın..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm
                                @error('bio') border-red-400 @enderror"
                        >{{ old('bio', $provider->bio ?? '') }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700
                                   text-white text-sm font-medium rounded-lg shadow-sm transition"
                        >
                            {{ $provider ? 'Profili Güncelle' : 'Profili Oluştur' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>