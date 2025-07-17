<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight tracking-wide">
            {{ __('📊 Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            {{-- Kartu Statistik --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $cards = [
                        ['label' => 'Total Karyawan', 'value' => $totalKaryawan, 'icon' => 'users', 'color' => 'blue'],
                        ['label' => 'Total Atasan', 'value' => $totalAtasan, 'icon' => 'user-group', 'color' => 'green'],
                        ['label' => 'Cuti Pending', 'value' => $pendingLeaves, 'icon' => 'document-text', 'color' => 'yellow'],
                        ['label' => 'Lembur Pending', 'value' => $pendingOvertimes, 'icon' => 'clock', 'color' => 'red'],
                    ];
                @endphp

                @foreach ($cards as $card)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg transform hover:scale-[1.03] transition duration-300 p-6 flex items-center gap-4">
                        <div class="p-4 rounded-full bg-{{ $card['color'] }}-100 text-{{ $card['color'] }}-600 shadow-inner">
                            <x-dynamic-component :component="'heroicon-o-' . $card['icon']" class="w-7 h-7" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">{{ $card['label'] }}</p>
                            <p class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $card['value'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Panel Aktivitas Terbaru --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 border-b border-gray-200 pb-2">📅 Aktivitas Terbaru</h3>
                <div class="space-y-4">
                    @forelse ($recentLogs as $log)
                        <div class="flex items-start space-x-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                            <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-sm font-semibold text-gray-700 dark:text-white">
                                {{ substr($log->user->name ?? 'S', 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800 dark:text-white">
                                    <span class="font-bold">{{ $log->user->name ?? 'Sistem' }}</span>
                                    {{ str_replace('_', ' ', $log->activity) }} 
                                    <span class="font-semibold text-indigo-600">#{{ $log->auditable_id }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">({{ class_basename($log->auditable_type) }})</span>.
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400">Belum ada aktivitas tercatat.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
