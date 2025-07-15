<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Selamat datang, {{ Auth::user()->name }}!
                    </h3>

                    {{-- Tampilkan Notifikasi --}}
                    @if (session('status'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('status') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                         <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <div class="text-center">
                            <p class="text-lg font-semibold">Absensi Hari Ini: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                            <p id="clock" class="text-4xl font-bold my-2"></p>
                            <p id="location-info" class="text-sm text-gray-500">Mendapatkan lokasi Anda...</p>

                            {{-- Form Absensi --}}
                            <form id="attendance-form" method="POST" action="{{ route('karyawan.attendance.store') }}" class="mt-4">
                                @csrf
                                <input type="hidden" name="location" id="location">

                                {{-- Logika Tombol Absen --}}
                                @if (!$todayAttendance)
                                    {{-- Jika belum absen masuk sama sekali --}}
                                    <x-primary-button id="submit-button" disabled>
                                        <div id="button-spinner" class="hidden animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-3"></div>
                                        Absen Masuk
                                    </x-primary-button>
                                @elseif (!$todayAttendance->check_out_time)
                                    {{-- Jika sudah absen masuk tapi belum absen pulang --}}
                                    <p class="text-green-600 font-semibold">Anda sudah absen masuk pada jam {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }}</p>
                                    <x-primary-button id="submit-button" class="mt-2 bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-800" disabled>
                                         <div id="button-spinner" class="hidden animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-3"></div>
                                        Absen Pulang
                                    </x-primary-button>
                                @else
                                    {{-- Jika sudah absen masuk dan pulang --}}
                                    <p class="text-blue-600 font-semibold">Anda sudah menyelesaikan absensi hari ini.</p>
                                    <p class="text-sm">Masuk: {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }} | Pulang: {{ \Carbon\Carbon::parse($todayAttendance->check_out_time)->format('H:i') }}</p>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const clockElement = document.getElementById('clock');
            const locationElement = document.getElementById('location');
            const locationInfoElement = document.getElementById('location-info');
            const submitButton = document.getElementById('submit-button');
            const buttonSpinner = document.getElementById('button-spinner');

            // Fungsi untuk update jam digital
            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                clockElement.textContent = `${hours}:${minutes}:${seconds}`;
            }

            // Panggil updateClock setiap detik
            setInterval(updateClock, 1000);
            updateClock(); // Panggil sekali saat load

            // Fungsi untuk mendapatkan lokasi GPS
            function getLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(showPosition, showError);
                } else {
                    locationInfoElement.textContent = "Geolocation tidak didukung oleh browser ini.";
                    locationInfoElement.classList.replace('text-gray-500', 'text-red-500');
                }
            }

            function showPosition(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                const locationString = `${lat},${lon}`;
                
                locationElement.value = locationString;
                locationInfoElement.textContent = `Lokasi Anda: ${lat.toFixed(5)}, ${lon.toFixed(5)}`;
                locationInfoElement.classList.replace('text-gray-500', 'text-green-500');
                if(submitButton) {
                    submitButton.disabled = false; // Aktifkan tombol jika lokasi berhasil didapat
                }
            }

            function showError(error) {
                let errorMessage = "Terjadi kesalahan saat mengambil lokasi.";
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = "Anda menolak permintaan untuk Geolocation.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = "Informasi lokasi tidak tersedia.";
                        break;
                    case error.TIMEOUT:
                        errorMessage = "Permintaan untuk mendapatkan lokasi pengguna timeout.";
                        break;
                    case error.UNKNOWN_ERROR:
                        errorMessage = "Terjadi kesalahan yang tidak diketahui.";
                        break;
                }
                locationInfoElement.textContent = errorMessage;
                locationInfoElement.classList.replace('text-gray-500', 'text-red-500');
            }

            // Panggil fungsi getLocation saat halaman dimuat
            getLocation();

            // Menampilkan spinner saat form disubmit
            const attendanceForm = document.getElementById('attendance-form');
            if (attendanceForm) {
                attendanceForm.addEventListener('submit', function() {
                    if(submitButton) {
                        submitButton.disabled = true;
                        buttonSpinner.classList.remove('hidden');
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>