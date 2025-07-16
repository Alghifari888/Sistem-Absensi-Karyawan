<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Proses Penggajian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- ====================================================== --}}
            {{--        PANEL INFORMASI PANDUAN PENGGAJIAN BARU         --}}
            {{-- ====================================================== --}}
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
                <div class="flex">
                    <div class="py-1"><x-heroicon-s-information-circle class="h-6 w-6 text-blue-500 mr-4"/></div>
                    <div>
                        <p class="font-bold">Panduan Halaman Penggajian</p>
                        <ul class="list-disc list-inside text-sm mt-2">
                            <li>Halaman ini secara otomatis menghitung gaji setiap karyawan berdasarkan data absensi dan lembur yang telah disetujui.</li>
                            <li>Gunakan filter untuk melihat data penggajian pada bulan dan tahun yang berbeda.</li>
                            <li>Pastikan **Gaji Pokok** dan **Tarif Lembur** untuk setiap karyawan sudah diatur di halaman **Manajemen User**.</li>
                            <li>Klik tombol **"Cetak Slip"** untuk mengunduh rincian gaji karyawan dalam format PDF.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Form Filter Bulan dan Tahun --}}
                    <form method="GET" action="{{ route('admin.payroll.index') }}" class="mb-6 flex items-end space-x-4">
                        <div>
                            <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan</label>
                            <select name="bulan" id="bulan" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $bulan ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                            <input type="number" name="tahun" id="tahun" value="{{ $tahun }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <x-primary-button type="submit">Filter</x-primary-button>
                    </form>

                    {{-- Tabel Penggajian --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji Pokok</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Upah Lembur</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gaji Total</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($payrollData as $data)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $data['user']->name }}</div>
                                            <div class="text-sm text-gray-500">Hadir: {{ $data['total_kehadiran'] }} hari | Lembur: {{ $data['total_jam_lembur'] }} jam</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">Rp {{ number_format($data['gaji_pokok'], 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">Rp {{ number_format($data['upah_lembur'], 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">Rp {{ number_format($data['gaji_total'], 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <a href="{{ route('admin.payroll.payslip', ['user' => $data['user']->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                Cetak Slip
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Tidak ada data untuk diproses pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
