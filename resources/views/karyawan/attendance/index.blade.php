<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Absensi & Slip Gaji') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Filter Periode + Download Slip Gaji --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('karyawan.attendance.index') }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label for="bulan" class="block text-sm font-medium text-gray-700">Bulan</label>
                        <select id="bulan" name="bulan" class="mt-1 block rounded-md border-gray-300 shadow-sm">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" @selected($bulan === $m)>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun</label>
                        <input id="tahun" type="number" name="tahun" value="{{ $tahun }}" min="2000" max="2100" class="mt-1 block w-28 rounded-md border-gray-300 shadow-sm">
                    </div>
                    <x-primary-button>Filter</x-primary-button>
                    <a href="{{ route('karyawan.payslip', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        Download Slip Gaji (PDF)
                    </a>
                </form>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 text-sm">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-500">Total Kehadiran</p>
                        <p class="text-lg font-bold text-gray-900">{{ $payroll['total_kehadiran'] }} Hari</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-500">Jam Lembur Disetujui</p>
                        <p class="text-lg font-bold text-gray-900">{{ $payroll['total_jam_lembur'] }} Jam</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-500">Gaji Pokok</p>
                        <p class="text-lg font-bold text-gray-900">Rp {{ number_format($payroll['gaji_pokok'], 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-lg">
                        <p class="text-indigo-700">Total Estimasi Gaji</p>
                        <p class="text-lg font-bold text-indigo-900">Rp {{ number_format($payroll['gaji_total'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Tabel Riwayat Absensi --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="py-2 px-4 text-left">Tanggal</th>
                                <th class="py-2 px-4 text-center">Jam Masuk</th>
                                <th class="py-2 px-4 text-center">Jam Pulang</th>
                                <th class="py-2 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attendances as $item)
                                @php
                                    $isLate = $item->check_in_time && \Carbon\Carbon::parse($item->check_in_time)->format('H:i:s') > \Carbon\Carbon::parse($jamMasukSetting)->format('H:i:s');
                                @endphp
                                <tr class="border-b">
                                    <td class="py-2 px-4">{{ \Carbon\Carbon::parse($item->attendance_date)->translatedFormat('d F Y') }}</td>
                                    <td class="py-2 px-4 text-center">{{ $item->check_in_time ? \Carbon\Carbon::parse($item->check_in_time)->format('H:i') : '-' }}</td>
                                    <td class="py-2 px-4 text-center">{{ $item->check_out_time ? \Carbon\Carbon::parse($item->check_out_time)->format('H:i') : '-' }}</td>
                                    <td class="py-2 px-4 text-center">
                                        @if ($isLate)
                                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Terlambat</span>
                                        @else
                                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">Tepat Waktu</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-500">Belum ada data absensi pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
