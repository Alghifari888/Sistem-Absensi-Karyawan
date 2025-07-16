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
                        <h3 class="font-semibold text-lg mb-2">Detail Pengajuan</h3>
                        <div class="border-t border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Karyawan</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $leave->user->name }}</dd>
                                </div>
                                <div class="bg-white px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Jenis</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ ucfirst($leave->type) }}</dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ \Carbon\Carbon::parse($leave->start_date)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($leave->end_date)->format('d F Y') }}</dd>
                                </div>
                                <div class="bg-white px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Alasan</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $leave->reason }}</dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Dokumen Bukti</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        @if($leave->proof_document)
                                            <a href="{{ asset('storage/' . $leave->proof_document) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Dokumen</a>
                                        @else
                                            Tidak ada
                                        @endif
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Status Saat Ini</dt>
                                    <dd class="mt-1 text-sm font-bold text-gray-900 sm:mt-0 sm:col-span-2">{{ ucfirst($leave->status) }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- Form Proses --}}
                    <form method="POST" action="{{ route('atasan.leaves.update', $leave) }}">
                        @csrf
                        @method('PUT')

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" :value="__('Setujui / Tolak Pengajuan')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="approved" @selected(old('status', $leave->status) == 'approved')>Setujui (Approve)</option>
                                <option value="rejected" @selected(old('status', $leave->status) == 'rejected')>Tolak (Reject)</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Catatan Atasan -->
                        <div class="mt-4">
                            <x-input-label for="approver_notes" :value="__('Catatan Atasan (Opsional)')" />
                            <textarea id="approver_notes" name="approver_notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('approver_notes', $leave->approver_notes) }}</textarea>
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
