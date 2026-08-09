<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form id="password-form" method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </div>
    </form>

    <script>
        document.getElementById('password-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;

            Swal.fire({
                title: 'Konfirmasi Perubahan Password',
                text: 'Apakah Anda yakin ingin mengubah password?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4F46E5',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Ubah Password',
                cancelButtonText: 'Batal',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const fdata = {
                        _method: 'PUT',
                        current_password: form.elements.current_password.value,
                        password: form.elements.password.value,
                        password_confirmation: form.elements.password_confirmation.value,
                        _token: csrfToken,
                    };

                    Swal.fire({
                        title: 'Mengubah Password...',
                        html: '<div class="spinner"></div>',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: (modal) => {
                            modal.querySelector('.swal2-confirm').style.display = 'none';
                        }
                    });

                    fetch(form.action, {
                        method: 'POST',
                        body: JSON.stringify(fdata),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        }
                    })
                    .then(async (response) => {
                        if (!response.ok) {
                            const data = await response.json().catch(() => ({}));
                            const message = data?.errors?.current_password?.[0]
                                || data?.errors?.password?.[0]
                                || data?.message
                                || 'Gagal mengubah password.';
                            throw new Error(message);
                        }
                        return response.json();
                    })
                    .then(() => {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Password berhasil di ubah',
                            icon: 'success',
                            confirmButtonColor: '#4F46E5',
                            confirmButtonText: 'OK',
                            allowOutsideClick: false,
                        }).then(() => {
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: error.message || 'Gagal mengubah password. Silakan coba lagi.',
                            icon: 'error',
                            confirmButtonColor: '#EF4444',
                        });
                    });
                }
            });
        });
    </script>

    <style>
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4F46E5;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</section>
