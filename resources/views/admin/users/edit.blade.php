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

                    <!-- Tombol Kembali -->
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Manajemen User
                    </a>

                    <form method="POST" action="{{ route('admin.users.update', $user) }}"
                          x-data="{ role: '{{ old('role', $user->role) }}', employmentType: '{{ old('employment_type', $user->employment_type ?? 'tetap') }}' }">
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

                        <!-- Password (opsional) -->
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password Baru')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" x-model="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="karyawan" @selected(old('role', $user->role) == 'karyawan')>Karyawan</option>
                                <option value="atasan" @selected(old('role', $user->role) == 'atasan')>Atasan</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Status Kepegawaian (hanya untuk Karyawan) -->
                        <div class="mt-4" x-show="role === 'karyawan'">
                            <x-input-label for="employment_type" :value="__('Status Kepegawaian')" />
                            <select id="employment_type" name="employment_type" x-model="employmentType" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="tetap" @selected(old('employment_type', $user->employment_type) == 'tetap')>Tetap</option>
                                <option value="kontrak" @selected(old('employment_type', $user->employment_type) == 'kontrak')>Kontrak</option>
                            </select>
                            <x-input-error :messages="$errors->get('employment_type')" class="mt-2" />
                        </div>

                        <!-- Tanggal Kontrak (hanya jika Kontrak) -->
                        <div class="mt-4 grid grid-cols-2 gap-4" x-show="role === 'karyawan' && employmentType === 'kontrak'">
                            <div>
                                <x-input-label for="contract_start_date" :value="__('Tanggal Mulai Kontrak')" />
                                <x-text-input id="contract_start_date" class="block mt-1 w-full" type="date" name="contract_start_date"
                                    :value="old('contract_start_date', optional($user->contract_start_date)->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('contract_start_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="contract_end_date" :value="__('Tanggal Berakhir Kontrak')" />
                                <x-text-input id="contract_end_date" class="block mt-1 w-full" type="date" name="contract_end_date"
                                    :value="old('contract_end_date', optional($user->contract_end_date)->format('Y-m-d'))" />
                                <x-input-error :messages="$errors->get('contract_end_date')" class="mt-2" />
                            </div>
                        </div>

                        @if($user->employment_type === 'kontrak' && $user->contract_end_date)
                            @php $sisaHari = $user->sisaHariKontrak(); @endphp
                            <div class="mt-2 text-xs {{ $sisaHari < 0 ? 'text-red-600' : ($sisaHari <= 30 ? 'text-orange-600' : 'text-gray-500') }}">
                                @if($sisaHari < 0)
                                    Kontrak sudah berakhir {{ abs($sisaHari) }} hari lalu.
                                @else
                                    Sisa kontrak: {{ $sisaHari }} hari lagi.
                                @endif
                            </div>
                        @endif

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

                        <!-- Jatah Cuti Tahunan -->
                        <div class="mt-4">
                            <x-input-label for="leave_quota" :value="__('Jatah Cuti Tahunan (hari)')" />
                            <x-text-input id="leave_quota" class="block mt-1 w-full" type="number" name="leave_quota" :value="old('leave_quota', $user->leave_quota)" required />
                            <x-input-error :messages="$errors->get('leave_quota')" class="mt-2" />
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