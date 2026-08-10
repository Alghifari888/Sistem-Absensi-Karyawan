<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah User Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.users.store') }}"
                          x-data="{ role: '{{ old('role', 'karyawan') }}', employmentType: '{{ old('employment_type', 'tetap') }}' }">
                        @csrf

                        <!-- Nama -->
                        <div>
                            <x-input-label for="name" :value="__('Nama')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" x-model="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="karyawan" @selected(old('role', 'karyawan') == 'karyawan')>Karyawan</option>
                                <option value="atasan" @selected(old('role') == 'atasan')>Atasan</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Status Kepegawaian (hanya untuk Karyawan) -->
                        <div class="mt-4" x-show="role === 'karyawan'">
                            <x-input-label for="employment_type" :value="__('Status Kepegawaian')" />
                            <select id="employment_type" name="employment_type" x-model="employmentType" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="tetap" @selected(old('employment_type', 'tetap') == 'tetap')>Tetap</option>
                                <option value="kontrak" @selected(old('employment_type') == 'kontrak')>Kontrak</option>
                            </select>
                            <x-input-error :messages="$errors->get('employment_type')" class="mt-2" />
                        </div>

                        <!-- Tanggal Kontrak (hanya jika Kontrak) -->
                        <div class="mt-4 grid grid-cols-2 gap-4" x-show="role === 'karyawan' && employmentType === 'kontrak'">
                            <div>
                                <x-input-label for="contract_start_date" :value="__('Tanggal Mulai Kontrak')" />
                                <x-text-input id="contract_start_date" class="block mt-1 w-full" type="date" name="contract_start_date" :value="old('contract_start_date')" />
                                <x-input-error :messages="$errors->get('contract_start_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="contract_end_date" :value="__('Tanggal Berakhir Kontrak')" />
                                <x-text-input id="contract_end_date" class="block mt-1 w-full" type="date" name="contract_end_date" :value="old('contract_end_date')" />
                                <x-input-error :messages="$errors->get('contract_end_date')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Simpan User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>