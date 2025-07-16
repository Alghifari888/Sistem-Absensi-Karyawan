<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ====================================================== --}}
            {{--         PANEL INFORMASI PANDUAN PENGATURAN BARU        --}}
            {{-- ====================================================== --}}
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
                <div class="flex">
                    <div class="py-1"><x-heroicon-s-information-circle class="h-6 w-6 text-blue-500 mr-4"/></div>
                    <div>
                        <p class="font-bold">Panduan Halaman Pengaturan Sistem</p>
                        <ul class="list-disc list-inside text-sm mt-2">
                            <li>Semua perubahan di halaman ini akan langsung mempengaruhi seluruh alur kerja sistem.</li>
                            <li>**Jam Masuk & Pulang:** Menentukan jam kerja standar dan validasi keterlambatan.</li>
                            <li>**Latitude & Longitude Kantor:** Titik pusat lokasi kantor untuk validasi absensi via GPS. Anda bisa mendapatkan koordinat ini dari Google Maps.</li>
                            <li>**Radius Toleransi:** Jarak maksimal (dalam meter) dari titik kantor di mana karyawan masih diizinkan untuk melakukan absensi.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    {{-- Notifikasi Sukses --}}
                    @if (session('status'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('status') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="jam_masuk" :value="__('Jam Masuk Kantor')" />
                                <x-text-input id="jam_masuk" class="block mt-1 w-full" type="time" name="jam_masuk" :value="old('jam_masuk', $settings['jam_masuk']->value ?? '')" required autofocus />
                                <x-input-error :messages="$errors->get('jam_masuk')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="jam_pulang" :value="__('Jam Pulang Kantor')" />
                                <x-text-input id="jam_pulang" class="block mt-1 w-full" type="time" name="jam_pulang" :value="old('jam_pulang', $settings['jam_pulang']->value ?? '')" required />
                                <x-input-error :messages="$errors->get('jam_pulang')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="lokasi_kantor_lat" :value="__('Latitude Kantor')" />
                                <x-text-input id="lokasi_kantor_lat" class="block mt-1 w-full" type="text" name="lokasi_kantor_lat" :value="old('lokasi_kantor_lat', $settings['lokasi_kantor_lat']->value ?? '')" placeholder="-6.224855" required />
                                <x-input-error :messages="$errors->get('lokasi_kantor_lat')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="lokasi_kantor_lon" :value="__('Longitude Kantor')" />
                                <x-text-input id="lokasi_kantor_lon" class="block mt-1 w-full" type="text" name="lokasi_kantor_lon" :value="old('lokasi_kantor_lon', $settings['lokasi_kantor_lon']->value ?? '')" placeholder="106.822295" required />
                                <x-input-error :messages="$errors->get('lokasi_kantor_lon')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="radius_absensi" :value="__('Radius Toleransi Absensi (meter)')" />
                                <x-text-input id="radius_absensi" class="block mt-1 w-full" type="number" name="radius_absensi" :value="old('radius_absensi', $settings['radius_absensi']->value ?? '')" placeholder="100" required />
                                <x-input-error :messages="$errors->get('radius_absensi')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Simpan Pengaturan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
