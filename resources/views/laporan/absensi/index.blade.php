<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Absensi Bulanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- PANEL INFORMASI PANDUAN BARU --}}
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
                <div class="flex">
                    <div class="py-1"><x-heroicon-s-information-circle class="h-6 w-6 text-blue-500 mr-4"/></div>
                    <div>
                        <p class="font-bold">Panduan Membaca Laporan Absensi</p>
                        <ul class="list-disc list-inside text-sm mt-2">
                            <li>Gunakan filter di atas untuk melihat laporan pada bulan dan tahun yang diinginkan.</li>
                            <li>Kolom **H** (Hijau) adalah total hari kehadiran karyawan pada periode tersebut.</li>
                            <li>Kolom **A** (Merah) adalah total hari karyawan tidak melakukan absensi (Alpha).</li>
                            <li>Sel absensi yang berwarna **merah muda** menandakan karyawan tersebut terlambat masuk.</li>
                            <li>Sel **L** (abu-abu) menandakan hari libur akhir pekan (Sabtu & Minggu).</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Form Filter Bulan dan Tahun --}}
                    <form method="GET" action="{{ route('laporan.absensi.index') }}" class="mb-6 flex items-end space-x-4">
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

                    {{-- Tabel Laporan --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                                    @php
                                        $daysInMonth = \Carbon\Carbon::createFromDate($tahun, $bulan)->daysInMonth;
                                    @endphp
                                    @for ($day = 1; $day <= $daysInMonth; $day++)
                                        <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $day }}</th>
                                    @endfor
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-green-100">H</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-red-100">A</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($karyawan as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                        @php
                                            $hadirCount = 0;
                                            $alphaCount = 0;
                                        @endphp
                                        @for ($day = 1; $day <= $daysInMonth; $day++)
                                            @php
                                                $currentDate = \Carbon\Carbon::createFromDate($tahun, $bulan, $day);
                                                $attendance = $user->attendances->firstWhere('attendance_date', $currentDate->toDateString());
                                                $isWeekend = $currentDate->isWeekend();
                                            @endphp
                                            <td class="px-2 py-4 whitespace-nowrap text-center text-sm 
                                                @if($isWeekend) bg-gray-200 @endif
                                                @if($attendance && \Carbon\Carbon::parse($attendance->check_in_time)->gt(\Carbon\Carbon::parse($jamMasukSetting))) bg-red-100 text-red-800 @endif">
                                                
                                                @if ($attendance)
                                                    {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }}
                                                    @php $hadirCount++; @endphp
                                                @elseif($isWeekend)
                                                    L
                                                @else
                                                    A
                                                    @php $alphaCount++; @endphp
                                                @endif
                                            </td>
                                        @endfor
                                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-bold bg-green-100">{{ $hadirCount }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-center text-sm font-bold bg-red-100">{{ $alphaCount }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
