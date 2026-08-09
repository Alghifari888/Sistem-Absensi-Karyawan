<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button id="delete-account-btn">{{ __('Delete Account') }}</x-danger-button>
</section>

<script>
    document.getElementById('delete-account-btn').addEventListener('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: '⚠️ Peringatan Penghapusan Akun',
            html: `
                <div style="text-align: left; margin: 20px 0;">
                    <p style="margin-bottom: 15px; font-weight: bold; color: #EF4444;">Tindakan ini TIDAK DAPAT DIBATALKAN!</p>
                    <div style="background: #FEE2E2; padding: 15px; border-radius: 8px; border-left: 4px solid #EF4444;">
                        <p style="margin: 10px 0; font-size: 14px;">
                            <strong>📋 Data yang akan dihapus:</strong>
                        </p>
                        <ul style="font-size: 13px; margin: 10px 0 0 0; padding-left: 20px;">
                            <li>Data akun akan dihapus PERMANEN dan tidak dapat dipulihkan</li>
                            <li>Semua informasi karyawan akan hilang selamanya</li>
                            <li>Riwayat login dan aktivitas akan dihapus</li>
                            <li>Tidak ada backup atau recovery option</li>
                        </ul>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Hapus Akun Saya',
            cancelButtonText: 'Batal',
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                showPasswordConfirmation();
            }
        });
    });

    function showPasswordConfirmation() {
        Swal.fire({
            title: 'Konfirmasi Password',
            html: `
                <div style="text-align: left; margin: 20px 0;">
                    <p style="margin-bottom: 15px; font-size: 14px;">Masukkan password Anda untuk mengkonfirmasi penghapusan akun:</p>
                    <input type="password" id="delete-password" placeholder="Masukkan password" style="
                        width: 100%;
                        padding: 10px;
                        border: 1px solid #D1D5DB;
                        border-radius: 6px;
                        font-size: 14px;
                        box-sizing: border-box;
                    " />
                    <div id="password-error" style="color: #EF4444; font-size: 13px; margin-top: 10px; display: none;"></div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Hapus Akun',
            cancelButtonText: 'Batal',
            allowOutsideClick: false,
            didOpen: () => {
                document.getElementById('delete-password').focus();
            },
            preConfirm: () => {
                const password = document.getElementById('delete-password').value;
                if (!password) {
                    document.getElementById('password-error').textContent = 'Password tidak boleh kosong';
                    document.getElementById('password-error').style.display = 'block';
                    return false;
                }
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                deleteAccount(result.value);
            }
        });
    }

    function deleteAccount(password) {
        Swal.fire({
            title: 'Menghapus Akun...',
            html: '<div class="spinner"></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: (modal) => {
                modal.querySelector('.swal2-confirm').style.display = 'none';
            }
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('{{ route("profile.destroy") }}', {
            method: 'POST',
            body: JSON.stringify({
                _method: 'DELETE',
                password: password,
                _token: csrfToken,
            }),
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
                const message = data?.errors?.password?.[0] || 'Gagal menghapus akun.';
                throw new Error(message);
            }
            return response.json();
        })
        .then(() => {
            Swal.fire({
                title: 'Akun Dihapus',
                text: 'Akun Anda telah dihapus secara permanen. Anda akan dialihkan ke halaman beranda.',
                icon: 'success',
                confirmButtonColor: '#4F46E5',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then(() => {
                window.location.href = '/';
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: error.message || 'Gagal menghapus akun. Password mungkin salah atau ada kesalahan sistem.',
                icon: 'error',
                confirmButtonColor: '#EF4444',
            });
        });
    }
</script>

<style>
    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #EF4444;
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
