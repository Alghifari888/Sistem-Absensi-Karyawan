<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

                            <div class="md:col-span-2">
                                <x-input-label for="radius_absensi" :value="__('Radius Toleransi Absensi (meter)')" />
                                <x-text-input id="radius_absensi" class="block mt-1 w-full" type="number" name="radius_absensi" :value="old('radius_absensi', $settings['radius_absensi']->value ?? '')" required />
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