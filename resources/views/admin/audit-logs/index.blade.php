<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Audit Log Aktivitas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4 text-gray-600">Halaman ini mencatat semua aktivitas penting yang terjadi di dalam sistem.</p>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Waktu</th>
                                    <th class="py-2 px-4 border-b text-left">User Pelaku</th>
                                    <th class="py-2 px-4 border-b text-left">Aktivitas</th>
                                    <th class="py-2 px-4 border-b text-left">Detail Perubahan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($logs as $log)
                                    <tr class="text-sm">
                                        <td class="py-2 px-4 border-b align-top whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        <td class="py-2 px-4 border-b align-top">{{ $log->user->name ?? 'System' }}</td>
                                        <td class="py-2 px-4 border-b align-top">
                                            <span class="font-semibold text-gray-800">{{ ucwords(str_replace('_', ' ', $log->activity)) }}</span>
                                            <span class="block text-xs text-gray-500">{{ str_replace('App\\Models\\', '', $log->auditable_type) }} #{{$log->auditable_id}}</span>
                                        </td>
                                        <td class="py-2 px-4 border-b align-top font-mono text-xs">
                                            @if($log->old_values)
                                                <strong class="text-red-600">Data Lama:</strong>
                                                <pre class="bg-red-50 p-2 rounded mt-1 whitespace-pre-wrap">@json($log->old_values, JSON_PRETTY_PRINT)</pre>
                                            @endif
                                            @if($log->new_values)
                                                 <strong class="text-green-600">Data Baru:</strong>
                                                <pre class="bg-green-50 p-2 rounded mt-1 whitespace-pre-wrap">@json($log->new_values, JSON_PRETTY_PRINT)</pre>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">Tidak ada aktivitas tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>