<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Proses Pengajuan Cuti / Izin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    
                    {{-- Detail Pengajuan --}}
                    <div>
                        <h3 class="font-semibold">Detail Pengajuan:</h3>
                        <p><strong>Karyawan:</strong> {{ $leave->user->name }}</p>
                        <p><strong>Jenis:</strong> {{ ucfirst($leave->type) }}</p>
                        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($leave->start_date)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($leave->end_date)->format('d F Y') }}</p>
                        <p><strong>Alasan:</strong> {{ $leave->reason }}</p>
                        <p><strong>Dokumen Bukti:</strong> 
                            @if($leave->proof_document)
                                <a href="{{ asset('storage/' . $leave->proof_document) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Dokumen</a>
                            @else
                                Tidak ada
                            @endif
                        </p>
                        <p><strong>Status Saat Ini:</strong> <span class="font-bold">{{ ucfirst($leave->status) }}</span></p>
                    </div>

                    <hr>

                    {{-- Form Proses --}}
                    <form method="POST" action="{{ route('atasan.leaves.update', $leave) }}">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="status" :value="__('Setujui / Tolak Pengajuan')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="approved" @selected(old('status', $leave->status) == 'approved')>Setujui (Approve)</option>
                                <option value="rejected" @selected(old('status', $leave->status) == 'rejected')>Tolak (Reject)</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <x-input-label for="approver_notes" :value="__('Catatan Atasan (Opsional)')" />
                            <textarea id="approver_notes" name="approver_notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('approver_notes', $leave->approver_notes) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>{{ __('Simpan Keputusan') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>