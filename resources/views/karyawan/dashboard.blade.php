<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Karyawan') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        {{-- KOTAK ABSENSI UTAMA --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">
                    Selamat datang, {{ Auth::user()->name }}!
                </h3>

                {{-- Tampilkan Notifikasi --}}
                @if (session('status'))
                    <div id="alert-success" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50" role="alert">
                       <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                       <span class="sr-only">Info</span>
                       <div class="ms-3 text-sm font-medium">
                         <span class="font-bold">Sukses!</span> {{ session('status') }}
                       </div>
                    </div>
                @endif
                @if (session('error'))
                     <div id="alert-error" class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50" role="alert">
                        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span class="sr-only">Info</span>
                        <div class="ms-3 text-sm font-medium">
                          <span class="font-bold">Gagal!</span> {{ session('error') }}
                        </div>
                     </div>
                @endif

                <div class="mt-6 border-t border-gray-200 pt-6">
                    <div class="text-center">
                        <p class="text-lg font-semibold">Absensi Hari Ini: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                        <p id="clock" class="text-4xl font-bold my-2"></p>
                        
                        {{-- Status Absensi --}}
                        @if ($todayAttendance && $todayAttendance->check_out_time)
                            <p class="text-blue-600 font-semibold">Anda sudah menyelesaikan absensi hari ini.</p>
                            <p class="text-sm">Masuk: {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }} | Pulang: {{ \Carbon\Carbon::parse($todayAttendance->check_out_time)->format('H:i') }}</p>
                        @elseif ($todayAttendance)
                            <p class="text-green-600 font-semibold">Anda sudah absen masuk pada jam {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }}</p>
                        @endif

                        {{-- Tombol Aksi & Form --}}
                        @if (!$todayAttendance || !$todayAttendance->check_out_time)
                            <div id="scanner-container" class="w-full max-w-sm mx-auto my-4 text-center" style="display: none;">
                                <div id="qr-reader" class="border-2 border-dashed rounded-lg p-2"></div>
                                {{-- Tombol Tutup Kamera --}}
                                <button type="button" id="close-scanner-button" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Tutup Kamera
                                </button>
                            </div>
    
                            <form id="attendance-form" method="POST" action="{{ route('karyawan.attendance.store') }}" class="hidden">
                                @csrf
                                <input type="hidden" name="location" id="location">
                                <input type="hidden" name="qr_token" id="qr_token">
                            </form>

                            <x-primary-button id="scan-button" class="mt-4">
                                <x-heroicon-s-qr-code class="w-5 h-5 mr-2" />
                                {{ !$todayAttendance ? 'Scan QR Absen Masuk' : 'Scan QR Absen Pulang' }}
                            </x-primary-button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Panel Informasi Panduan --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="font-semibold text-lg border-b pb-3 mb-4">Panduan Penggunaan Sistem</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                        
                        <div class="space-y-2 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <x-heroicon-o-qr-code class="w-6 h-6 mr-2 text-indigo-600"/>
                                <h4 class="font-bold">Absensi Harian</h4>
                            </div>
                            <p class="text-gray-600">
                                Untuk melakukan absensi masuk atau pulang, klik tombol **"Scan QR"** di atas dan arahkan kamera Anda ke QR Code yang disediakan. Pastikan izin lokasi dan kamera aktif.
                            </p>
                        </div>

                        <div class="space-y-2 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <x-heroicon-o-clock class="w-6 h-6 mr-2 text-indigo-600"/>
                                <h4 class="font-bold">Pengajuan Lembur</h4>
                            </div>
                            <p class="text-gray-600">
                                Gunakan menu **"Lembur"** di sidebar untuk mengajukan jam kerja tambahan. Anda dapat melihat riwayat dan status persetujuan dari semua pengajuan lembur Anda di halaman tersebut.
                            </p>
                        </div>

                        <div class="space-y-2 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <x-heroicon-o-document-text class="w-6 h-6 mr-2 text-indigo-600"/>
                                <h4 class="font-bold">Pengajuan Cuti & Izin</h4>
                            </div>
                            <p class="text-gray-600">
                                Gunakan menu **"Cuti & Izin"** untuk mengajukan permohonan tidak masuk kerja. Untuk izin sakit, jangan lupa untuk mengunggah dokumen bukti seperti surat keterangan dokter.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Library untuk QR Scanner --}}
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const clockElement = document.getElementById('clock');
            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                clockElement.textContent = `${hours}:${minutes}:${seconds}`;
            }
            setInterval(updateClock, 1000);
            updateClock();

            setTimeout(() => {
                document.getElementById('alert-success')?.remove();
                document.getElementById('alert-error')?.remove();
            }, 5000);

            const scanButton = document.getElementById('scan-button');
            const scannerContainer = document.getElementById('scanner-container');
            const closeScannerButton = document.getElementById('close-scanner-button');
            let html5QrCode;

            function stopScanner() {
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => {
                        scannerContainer.style.display = 'none';
                        scanButton.style.display = 'inline-flex';
                    }).catch(err => console.error("Gagal menghentikan kamera.", err));
                }
            }

            if (scanButton) {
                scanButton.addEventListener('click', function() {
                    scannerContainer.style.display = 'block';
                    this.style.display = 'none';

                    if (!html5QrCode) {
                        html5QrCode = new Html5Qrcode("qr-reader");
                    }
                    
                    html5QrCode.start(
                        { facingMode: "environment" },
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        onScanSuccess,
                        (error) => { /* Abaikan error scan, biarkan kamera terus berjalan */ }
                    ).catch(err => {
                        alert("Tidak dapat memulai kamera. Pastikan Anda memberikan izin.");
                        stopScanner(); // Hentikan jika gagal memulai
                    });
                });
            }
            
            if (closeScannerButton) {
                closeScannerButton.addEventListener('click', stopScanner);
            }

            function onScanSuccess(decodedText, decodedResult) {
                // Hentikan scanner setelah berhasil
                stopScanner();
                
                // Isi form dan submit
                document.getElementById('qr_token').value = decodedText;
                
                navigator.geolocation.getCurrentPosition(position => {
                    const locationString = `${position.coords.latitude},${position.coords.longitude}`;
                    document.getElementById('location').value = locationString;
                    document.getElementById('attendance-form').submit();
                }, () => {
                    alert('Gagal mendapatkan lokasi GPS. Pastikan izin lokasi aktif.');
                    scanButton.style.display = 'inline-flex'; // Munculkan lagi tombol scan jika GPS gagal
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
