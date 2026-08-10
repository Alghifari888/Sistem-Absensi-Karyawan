<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen User & Gaji') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
                <div class="flex">
                    <div class="py-1"><x-heroicon-s-information-circle class="h-6 w-6 text-blue-500 mr-4"/></div>
                    <div>
                        <p class="font-bold">Panduan Halaman Manajemen User</p>
                        <ul class="list-disc list-inside text-sm mt-2">
                            <li>Halaman ini digunakan untuk mengelola data semua pengguna dengan role **Karyawan** dan **Atasan**.</li>
                            <li>Gunakan tombol **"Tambah User Baru"** untuk mendaftarkan akun baru ke dalam sistem.</li>
                            <li>Klik **"Edit"** untuk mengubah detail, role, status kepegawaian, Gaji Pokok, Tarif Lembur, dan Jatah Cuti.</li>
                            <li>Klik **"Nonaktifkan"** untuk menonaktifkan akun (bukan menghapus) — data histori tetap aman.</li>
                            <li>Klik **"Cek Kontrak Berakhir"** untuk memeriksa & menonaktifkan otomatis karyawan kontrak yang masa kontraknya sudah lewat.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-end mb-4 gap-3">
                        <form method="POST" action="{{ route('admin.users.check-expired-contracts') }}"
                              onsubmit="return confirm('Jalankan pengecekan kontrak yang sudah berakhir sekarang?')">
                            @csrf
                            <x-secondary-button type="submit">
                                {{ __('Cek Kontrak Berakhir') }}
                            </x-secondary-button>
                        </form>
                        <a href="{{ route('admin.users.create') }}">
                            <x-primary-button>
                                {{ __('Tambah User Baru') }}
                            </x-primary-button>
                        </a>
                    </div>

                    @if (session('status'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                             x-transition.opacity.duration.500ms
                             class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('status') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                             x-transition.opacity.duration.500ms
                             class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 border-b">Nama</th>
                                    <th class="py-2 px-4 border-b">Email</th>
                                    <th class="py-2 px-4 border-b">Role</th>
                                    <th class="py-2 px-4 border-b">Kepegawaian</th>
                                    <th class="py-2 px-4 border-b">Status</th>
                                    <th class="py-2 px-4 border-b">Gaji Pokok</th>
                                    <th class="py-2 px-4 border-b">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr class="text-center">
                                        <td class="py-2 px-4 border-b text-left">{{ $user->name }}</td>
                                        <td class="py-2 px-4 border-b text-left">{{ $user->email }}</td>
                                        <td class="py-2 px-4 border-b">{{ ucfirst($user->role) }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if($user->role === 'karyawan' && $user->employment_type)
                                                @php $sisaHari = $user->sisaHariKontrak(); @endphp
                                                @if($user->employment_type === 'tetap')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Tetap</span>
                                                @elseif($sisaHari !== null && $sisaHari < 0)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800" title="Kontrak berakhir {{ abs($sisaHari) }} hari lalu">Kontrak Berakhir</span>
                                                @elseif($sisaHari !== null && $sisaHari <= 30)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800" title="{{ $sisaHari }} hari lagi">Kontrak ({{ $sisaHari }} hr lagi)</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Kontrak</span>
                                                @endif
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            @if($user->is_active)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700"
                                                      title="{{ $user->deactivated_reason }}">Non-aktif</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b text-right">Rp {{ number_format($user->gaji_pokok, 2, ',', '.') }}</td>
                                        <td class="py-2 px-4 border-b space-x-3">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <button type="button"
                                                class="toggle-status-btn text-sm {{ $user->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}"
                                                data-url="{{ route('admin.users.toggle-status', $user) }}"
                                                data-active="{{ $user->is_active ? '1' : '0' }}"
                                                data-name="{{ $user->name }}">
                                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Tidak ada data user.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form tersembunyi untuk submit toggle status --}}
    <form id="toggle-status-form" method="POST" style="display:none;">
        @csrf
        @method('PATCH')
        <input type="hidden" name="deactivated_reason" id="deactivated_reason_input">
    </form>

    @push('scripts')
    <script>
        document.querySelectorAll('.toggle-status-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const url = this.dataset.url;
                const isActive = this.dataset.active === '1';
                const name = this.dataset.name;
                const form = document.getElementById('toggle-status-form');

                if (isActive) {
                    Swal.fire({
                        title: `Nonaktifkan ${name}?`,
                        input: 'textarea',
                        inputLabel: 'Alasan nonaktif (wajib diisi)',
                        inputPlaceholder: 'Contoh: Resign per 10 Agustus 2026',
                        inputValidator: (value) => !value ? 'Alasan wajib diisi!' : undefined,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Nonaktifkan',
                        confirmButtonColor: '#dc2626',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('deactivated_reason_input').value = result.value;
                            form.action = url;
                            form.submit();
                        }
                    });
                } else {
                    Swal.fire({
                        title: `Aktifkan kembali ${name}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Aktifkan',
                        confirmButtonColor: '#16a34a',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.action = url;
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>