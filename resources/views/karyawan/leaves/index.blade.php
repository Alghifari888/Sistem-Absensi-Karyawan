<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Pengajuan Cuti & Izin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('karyawan.leaves.create') }}">
                            <x-primary-button>{{ __('Ajukan Cuti/Izin') }}</x-primary-button>
                        </a>
                    </div>
                    
                    @if (session('status'))
                        {{-- Notifikasi --}}
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 border-b">Jenis</th>
                                    <th class="py-2 px-4 border-b">Tanggal</th>
                                    <th class="py-2 px-4 border-b">Bukti</th>
                                    <th class="py-2 px-4 border-b">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leaves as $leave)
                                    <tr class="text-center">
                                        <td class="py-2 px-4 border-b">{{ ucfirst($leave->type) }}</td>
                                        <td class="py-2 px-4 border-b">{{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/Y') }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if($leave->proof_document)
                                                <a href="{{ asset('storage/' . $leave->proof_document) }}" target="_blank" class="text-blue-600 hover:underline">Lihat</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            {{-- Status Badge --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">Belum ada riwayat pengajuan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     <div class="mt-4">{{ $leaves->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>