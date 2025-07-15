<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('QR Code Absensi Hari Ini') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    <p class="mb-4">Tunjukkan QR Code ini kepada karyawan untuk melakukan absensi.</p>
                    
                    {{-- Generate QR Code dari token --}}
                    <div class="inline-block p-4 border rounded-lg">
                        {!! QrCode::size(300)->generate($qrToken) !!}
                    </div>

                    <p class="mt-4 text-sm text-gray-600">
                        Token Hari Ini: <br>
                        <span class="font-mono">{{ $qrToken }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>