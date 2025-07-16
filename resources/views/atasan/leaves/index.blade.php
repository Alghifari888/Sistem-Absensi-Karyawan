<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Pengajuan Cuti & Izin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session('status'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('status') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 border-b">Karyawan</th>
                                    <th class="py-2 px-4 border-b">Jenis</th>
                                    <th class="py-2 px-4 border-b">Tanggal</th>
                                    <th class="py-2 px-4 border-b">Bukti</th>
                                    <th class="py-2 px-4 border-b">Status</th>
                                    <th class="py-2 px-4 border-b">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leaves as $leave)
                                    <tr class="text-center">
                                        <td class="py-2 px-4 border-b">{{ $leave->user->name }}</td>
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
                                            @if($leave->status == 'pending')
                                                <span class="bg-yellow-200 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Pending</span>
                                            @elseif($leave->status == 'approved')
                                                <span class="bg-green-200 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Approved</span>
                                            @else
                                                <span class="bg-red-200 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <a href="{{ route('atasan.leaves.edit', $leave) }}" class="text-indigo-600 hover:text-indigo-900">Proses</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Tidak ada pengajuan.</td>
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
