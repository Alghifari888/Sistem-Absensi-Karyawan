<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Audit Log Aktivitas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ====================================================== --}}
            {{--           PANEL INFORMASI PANDUAN AUDIT LOG          --}}
            {{-- ====================================================== --}}
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
                <div class="flex">
                    <div class="py-1"><x-heroicon-s-information-circle class="h-6 w-6 text-blue-500 mr-4"/></div>
                    <div>
                        <p class="font-bold">Panduan Halaman Audit Log</p>
                        <ul class="list-disc list-inside text-sm mt-2">
                            <li>Halaman ini adalah catatan "CCTV" digital untuk semua aktivitas penting di dalam sistem.</li>
                            <li>**User Pelaku:** Siapa yang melakukan perubahan (misal: Admin atau Atasan).</li>
                            <li>**Aktivitas:** Tindakan yang dilakukan (misal: memproses cuti, memperbarui gaji).</li>
                            <li>**Detail Perubahan:** Menampilkan data sebelum (`Data Lama`) dan sesudah (`Data Baru`) perubahan untuk pelacakan yang akurat.</li>
                            <li>Gunakan panel pencarian di bawah untuk menyaring log berdasarkan waktu, pelaku, jenis aktivitas, atau kata kunci di detail perubahan.</li>
                            <li>Log ini dibuat secara otomatis oleh sistem dan tidak dapat diubah demi menjaga integritas data.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- ====================================================== --}}
            {{--                  PANEL FILTER PENCARIAN               --}}
            {{-- ====================================================== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('admin.audit-logs.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                            <!-- Dari Tanggal -->
                            <div>
                                <x-input-label for="date_from" :value="__('Dari Tanggal')" />
                                <x-text-input id="date_from" class="block mt-1 w-full text-sm" type="date" name="date_from" :value="request('date_from')" />
                            </div>

                            <!-- Sampai Tanggal -->
                            <div>
                                <x-input-label for="date_to" :value="__('Sampai Tanggal')" />
                                <x-text-input id="date_to" class="block mt-1 w-full text-sm" type="date" name="date_to" :value="request('date_to')" />
                            </div>

                            <!-- User Pelaku -->
                            <div>
                                <x-input-label for="user_id" :value="__('User Pelaku')" />
                                <select id="user_id" name="user_id" class="block mt-1 w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Semua User</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Aktivitas -->
                            <div>
                                <x-input-label for="activity" :value="__('Aktivitas')" />
                                <select id="activity" name="activity" class="block mt-1 w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Semua Aktivitas</option>
                                    @foreach ($activities as $act)
                                        <option value="{{ $act }}" @selected(request('activity') == $act)>{{ ucwords(str_replace('_', ' ', $act)) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Detail Perubahan (kata kunci) -->
                            <div>
                                <x-input-label for="detail" :value="__('Cari di Detail Perubahan')" />
                                <x-text-input id="detail" class="block mt-1 w-full text-sm" type="text" name="detail" :value="request('detail')" placeholder="mis. nama, email..." />
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mt-4">
                            <x-primary-button type="submit">
                                {{ __('Cari') }}
                            </x-primary-button>
                            @if(request()->anyFilled(['date_from', 'date_to', 'user_id', 'activity', 'detail']))
                                <a href="{{ route('admin.audit-logs.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                                    Reset Filter
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-3 text-sm text-gray-500">
                        Menampilkan {{ $logs->total() }} hasil.
                    </div>

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
                                        <td colspan="4" class="text-center py-4">Tidak ada aktivitas yang cocok dengan pencarian.</td>
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