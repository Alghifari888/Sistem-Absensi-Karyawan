<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A11.953 11.953 0 0112 15c2.5 0 4.847.76 6.879 2.06M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{ __('Profil Saya') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Update Profile --}}
            <div class="p-6 bg-white dark:bg-gray-900 shadow-lg rounded-2xl">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-100 mb-4">🔧 Perbarui Informasi Profil</h3>
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="p-6 bg-white dark:bg-gray-900 shadow-lg rounded-2xl">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-100 mb-4">🔒 Ubah Password</h3>
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="p-6 bg-white dark:bg-gray-900 shadow-lg rounded-2xl">
                <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4">⚠️ Hapus Akun</h3>
                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
