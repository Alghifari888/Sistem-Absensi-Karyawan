<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Kartu Statistik -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Karyawan -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <x-heroicon-o-users class="w-6 h-6"/>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Karyawan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalKaryawan }}</p>
                    </div>
                </div>
                <!-- Total Atasan -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <x-heroicon-o-user-group class="w-6 h-6"/>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Atasan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalAtasan }}</p>
                    </div>
                </div>
                <!-- Pengajuan Cuti Pending -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <x-heroicon-o-document-text class="w-6 h-6"/>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Cuti Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingLeaves }}</p>
                    </div>
                </div>
                <!-- Pengajuan Lembur Pending -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                        <x-heroicon-o-clock class="w-6 h-6"/>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Lembur Pending</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingOvertimes }}</p>
                    </div>
                </div>
            </div>

            <!-- Panel Aktivitas Terbaru -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg mb-4">Aktivitas Terbaru</h3>
                    <div class="space-y-4">
                        @forelse ($recentLogs as $log)
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-200">
                                        <span class="text-sm font-medium leading-none text-gray-600">{{ substr($log->user->name ?? 'S', 0, 1) }}</span>
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-800">
                                        <span class="font-bold">{{ $log->user->name ?? 'Sistem' }}</span>
                                        {{ str_replace('_', ' ', $log->activity) }} 
                                        <span class="font-semibold">{{ str_replace('App\\Models\\', '', $log->auditable_type) }} #{{$log->auditable_id}}</span>.
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500">Belum ada aktivitas tercatat.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
