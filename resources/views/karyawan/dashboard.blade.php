<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">
                        Selamat datang, {{ Auth::user()->name }}!
                    </h3>

                    {{-- Tampilkan Notifikasi --}}
                    @if (session('status'))
                        {{-- Notifikasi Sukses --}}
                        <div id="alert-success" class="flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50" role="alert">
                           {{-- icon & text --}}
                           <span class="sr-only">Info</span>
                           <div>
                             <span class="font-medium">Sukses!</span> {{ session('status') }}
                           </div>
                        </div>
                    @endif
                    @if (session('error'))
                         {{-- Notifikasi Error --}}
                         <div id="alert-error" class="flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50" role="alert">
                            {{-- icon & text --}}
                            <span class="sr-only">Info</span>
                            <div>
                              <span class="font-medium">Gagal!</span> {{ session('error') }}
                            </div>
                         </div>
                    @endif

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <div class="text-center">
                            <p class="text-lg font-semibold">Absensi Hari Ini: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                            <p id="clock" class="text-4xl font-bold my-2"></p>
                            
                            {{-- Status Absensi --}}
                            @if ($todayAttendance && $todayAttendance->check_out_time)
                                {{-- Jika sudah absen masuk dan pulang --}}
                                <p class="text-blue-600 font-semibold">Anda sudah menyelesaikan absensi hari ini.</p>
                                <p class="text-sm">Masuk: {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }} | Pulang: {{ \Carbon\Carbon::parse($todayAttendance->check_out_time)->format('H:i') }}</p>
                            @elseif ($todayAttendance)
                                {{-- Jika sudah absen masuk tapi belum pulang --}}
                                <p class="text-green-600 font-semibold">Anda sudah absen masuk pada jam {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }}</p>
                            @endif

                            {{-- Tombol Aksi & Form --}}
                            @if (!$todayAttendance || !$todayAttendance->check_out_time)
                                <div id="scanner-container" class="w-full max-w-sm mx-auto my-4 border-2 border-dashed rounded-lg p-2" style="display: none;">
                                    <div id="qr-reader"></div>
                                </div>
    
                                <form id="attendance-form" method="POST" action="{{ route('karyawan.attendance.store') }}" class="hidden">
                                    @csrf
                                    <input type="hidden" name="location" id="location">
                                    <input type="hidden" name="qr_token" id="qr_token">
                                </form>

                                <x-primary-button id="scan-button">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM12 4v.01"></path></svg>
                                    {{ !$todayAttendance ? 'Scan QR Absen Masuk' : 'Scan QR Absen Pulang' }}
                                </x-primary-button>
                            @endif
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
            // ... (fungsi jam digital & notifikasi sama seperti sebelumnya)
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

            // Sembunyikan notifikasi setelah 5 detik
            setTimeout(() => {
                document.getElementById('alert-success')?.remove();
                document.getElementById('alert-error')?.remove();
            }, 5000);

            // ======================================================
            //           LOGIKA BARU UNTUK QR SCANNER
            // ======================================================
            const scanButton = document.getElementById('scan-button');
            const scannerContainer = document.getElementById('scanner-container');
            let html5QrCode;

            if (scanButton) {
                scanButton.addEventListener('click', function() {
                    scannerContainer.style.display = 'block'; // Tampilkan container scanner
                    this.style.display = 'none'; // Sembunyikan tombol scan

                    if (!html5QrCode) {
                        html5QrCode = new Html5Qrcode("qr-reader");
                    }
                    
                    html5QrCode.start(
                        { facingMode: "environment" }, // Gunakan kamera belakang
                        {
                            fps: 10,    // Optional, frame per second
                            qrbox: { width: 250, height: 250 }  // Optional, ukuran box scan
                        },
                        onScanSuccess,
                        onScanFailure
                    ).catch(err => {
                        alert("Tidak dapat memulai kamera. Pastikan Anda memberikan izin.");
                        console.error(err);
                        // Kembalikan tombol jika gagal memulai kamera
                        scannerContainer.style.display = 'none';
                        scanButton.style.display = 'inline-flex';
                    });
                });
            }

            function onScanSuccess(decodedText, decodedResult) {
                // Handle a successful scan
                console.log(`Scan result: ${decodedText}`, decodedResult);
                
                // Hentikan scanner
                html5QrCode.stop().then(ignore => {
                    scannerContainer.style.display = 'none';
                    scanButton.style.display = 'inline-flex';
                    
                    // Isi form dan submit
                    document.getElementById('qr_token').value = decodedText;
                    
                    // Dapatkan lokasi GPS sebelum submit
                    navigator.geolocation.getCurrentPosition(position => {
                        const locationString = `${position.coords.latitude},${position.coords.longitude}`;
                        document.getElementById('location').value = locationString;
                        
                        // Submit form
                        document.getElementById('attendance-form').submit();
                    }, () => {
                        alert('Gagal mendapatkan lokasi GPS. Pastikan izin lokasi aktif.');
                    });

                }).catch(err => console.error(err));
            }

            function onScanFailure(error) {
                // handle scan failure, usually better to ignore and keep scanning.
                // console.warn(`Code scan error = ${error}`);
            }
        });
    </script>
    @endpush
</x-app-layout>