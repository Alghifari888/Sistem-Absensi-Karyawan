@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/vanilla-tilt@1.7.2/dist/vanilla-tilt.min.js"></script>
<script>
    VanillaTilt.init(document.querySelectorAll("[data-tilt]"), {
        max: 8,
        speed: 400,
        glare: true,
        "max-glare": 0.2
    });
</script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight tracking-wide">
            {{ __('📊 Dashboard Atasan') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Welcome Card -->
            <div class="bg-white/70 backdrop-blur-lg shadow-xl rounded-xl p-6" data-tilt>
                <h3 class="text-xl font-bold text-gray-800">Selamat datang kembali, {{ Auth::user()->name }}!</h3>
                <p class="text-gray-600 mt-1 text-sm">Berikut adalah ringkasan tugas dan status tim Anda.</p>
            </div>

            <!-- Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-yellow-50 shadow-md p-6 rounded-xl flex items-center hover:scale-105 transition" data-tilt>
                    <div class="p-4 bg-yellow-200 rounded-full text-yellow-700 mr-4">
                        <x-heroicon-o-document-text class="w-8 h-8" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Cuti & Izin Pending</p>
                        <p class="text-3xl font-extrabold text-yellow-900">{{ $pendingLeaves }}</p>
                    </div>
                </div>

                <div class="bg-orange-50 shadow-md p-6 rounded-xl flex items-center hover:scale-105 transition" data-tilt>
                    <div class="p-4 bg-orange-200 rounded-full text-orange-700 mr-4">
                        <x-heroicon-o-clock class="w-8 h-8" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-orange-800">Lembur Pending</p>
                        <p class="text-3xl font-extrabold text-orange-900">{{ $pendingOvertimes }}</p>
                    </div>
                </div>
            </div>

            <!-- Pengajuan Menunggu -->
            <div class="bg-white/80 backdrop-blur-md shadow-lg rounded-xl p-6 space-y-4" data-tilt>
                <h3 class="text-lg font-semibold text-gray-800">📥 Tugas Menunggu Persetujuan</h3>

                @if($recentLeaves->isEmpty() && $recentOvertimes->isEmpty())
                    <p class="text-center text-gray-500 py-6 italic">Tidak ada pengajuan saat ini. 👏</p>
                @else
                    {{-- Cuti --}}
                    @foreach ($recentLeaves as $leave)
                        <div class="flex items-center justify-between bg-yellow-50 p-4 rounded-md shadow hover:scale-[1.01] transition-all">
                            <div class="flex items-center">
                                <x-heroicon-s-document-text class="w-6 h-6 text-yellow-500 mr-3" />
                                <div>
                                    <p class="text-sm font-semibold text-yellow-900">{{ $leave->user->name }} mengajukan {{ $leave->type }}</p>
                                    <p class="text-xs text-yellow-700">Tanggal: {{ \Carbon\Carbon::parse($leave->start_date)->format('d/m/y') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d/m/y') }}</p>
                                </div>
                            </div>
                            <a href="{{ route('atasan.leaves.edit', $leave) }}" class="text-sm font-semibold text-indigo-600 hover:underline">Proses</a>
                        </div>
                    @endforeach

                    {{-- Lembur --}}
                    @foreach ($recentOvertimes as $overtime)
                        <div class="flex items-center justify-between bg-orange-50 p-4 rounded-md shadow hover:scale-[1.01] transition-all">
                            <div class="flex items-center">
                                <x-heroicon-s-clock class="w-6 h-6 text-orange-500 mr-3" />
                                <div>
                                    <p class="text-sm font-semibold text-orange-900">{{ $overtime->user->name }} mengajukan lembur</p>
                                    <p class="text-xs text-orange-700">Tanggal: {{ \Carbon\Carbon::parse($overtime->overtime_date)->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <a href="{{ route('atasan.overtime.edit', $overtime) }}" class="text-sm font-semibold text-indigo-600 hover:underline">Proses</a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
