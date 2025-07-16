<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User & Gaji') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nama -->
                        <div>
                            <x-input-label for="name" :value="__('Nama')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="karyawan" @selected(old('role', $user->role) == 'karyawan')>Karyawan</option>
                                <option value="atasan" @selected(old('role', $user->role) == 'atasan')>Atasan</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Gaji Pokok -->
                        <div class="mt-4">
                            <x-input-label for="gaji_pokok" :value="__('Gaji Pokok')" />
                            <x-text-input id="gaji_pokok" class="block mt-1 w-full" type="number" name="gaji_pokok" :value="old('gaji_pokok', $user->gaji_pokok)" required />
                            <x-input-error :messages="$errors->get('gaji_pokok')" class="mt-2" />
                        </div>

                        <!-- Tarif Lembur -->
                        <div class="mt-4">
                            <x-input-label for="tarif_lembur_per_jam" :value="__('Tarif Lembur per Jam')" />
                            <x-text-input id="tarif_lembur_per_jam" class="block mt-1 w-full" type="number" name="tarif_lembur_per_jam" :value="old('tarif_lembur_per_jam', $user->tarif_lembur_per_jam)" required />
                            <x-input-error :messages="$errors->get('tarif_lembur_per_jam')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>