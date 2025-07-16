<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Pengajuan Lembur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('karyawan.overtime.create') }}">
                            <x-primary-button>
                                {{ __('Ajukan Lembur Baru') }}
                            </x-primary-button>
                        </a>
                    </div>
                    
                    @if (session('status'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('status') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 border-b">Tanggal</th>
                                    <th class="py-2 px-4 border-b">Jam Mulai</th>
                                    <th class="py-2 px-4 border-b">Jam Selesai</th>
                                    <th class="py-2 px-4 border-b">Alasan</th>
                                    <th class="py-2 px-4 border-b">Status</th>
                                    <th class="py-2 px-4 border-b">Catatan Atasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- PERBAIKAN: Menggunakan variabel $overtimes, bukan $leaves --}}
                                @forelse ($overtimes as $overtime)
                                    <tr class="text-center">
                                        <td class="py-2 px-4 border-b">{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('d/m/Y') }}</td>
                                        <td class="py-2 px-4 border-b">{{ $overtime->start_time }}</td>
                                        <td class="py-2 px-4 border-b">{{ $overtime->end_time }}</td>
                                        <td class="py-2 px-4 border-b text-left">{{ $overtime->reason }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if($overtime->status == 'pending')
                                                <span class="bg-yellow-200 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Pending</span>
                                            @elseif($overtime->status == 'approved')
                                                <span class="bg-green-200 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Approved</span>
                                            @else
                                                <span class="bg-red-200 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b text-left">{{ $overtime->approver_notes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Belum ada riwayat pengajuan lembur.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4">
                        {{ $overtimes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
