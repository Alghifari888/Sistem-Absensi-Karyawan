<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Proses Pengajuan Lembur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    
                    {{-- Detail Pengajuan --}}
                    <div>
                        <h3 class="font-semibold">Detail Pengajuan:</h3>
                        <p><strong>Karyawan:</strong> {{ $overtime->user->name }}</p>
                        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($overtime->overtime_date)->format('d F Y') }}</p>
                        <p><strong>Waktu:</strong> {{ $overtime->start_time }} s/d {{ $overtime->end_time }}</p>
                        <p><strong>Alasan:</strong> {{ $overtime->reason }}</p>
                        <p><strong>Status Saat Ini:</strong> <span class="font-bold">{{ ucfirst($overtime->status) }}</span></p>
                    </div>

                    <hr>

                    {{-- Form Proses --}}
                    <form method="POST" action="{{ route('atasan.overtime.update', $overtime) }}">
                        @csrf
                        @method('PUT')

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" :value="__('Setujui / Tolak Pengajuan')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="approved" @selected(old('status', $overtime->status) == 'approved')>Setujui (Approve)</option>
                                <option value="rejected" @selected(old('status', $overtime->status) == 'rejected')>Tolak (Reject)</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Catatan Atasan -->
                        <div class="mt-4">
                            <x-input-label for="approver_notes" :value="__('Catatan (Opsional)')" />
                            <textarea id="approver_notes" name="approver_notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('approver_notes', $overtime->approver_notes) }}</textarea>
                            <x-input-error :messages="$errors->get('approver_notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Simpan Keputusan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>