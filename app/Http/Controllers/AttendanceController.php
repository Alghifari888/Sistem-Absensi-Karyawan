<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
        ]);

        $settings = Setting::all()->keyBy('key');
        
        // ======================================================
        // VALIDASI JARAK LOKASI (GPS)
        // ======================================================

        // Ambil pengaturan lokasi & radius dari database
        $lokasiKantorLat = $settings['lokasi_kantor_lat']->value ?? null;
        $lokasiKantorLon = $settings['lokasi_kantor_lon']->value ?? null;
        $radiusAbsensi = $settings['radius_absensi']->value ?? 100;

        // Pisahkan latitude dan longitude dari request
        list($latitude, $longitude) = explode(',', $request->location);
        
        // Cek jika pengaturan lokasi kantor sudah ada
        if ($lokasiKantorLat && $lokasiKantorLon) {
            // Panggil fungsi helper untuk menghitung jarak
            $jarak = calculateDistance(
                $latitude,
                $longitude,
                $lokasiKantorLat,
                $lokasiKantorLon
            );

            // Jika jarak lebih besar dari radius yang diizinkan, tolak absensi
            if ($jarak > $radiusAbsensi) {
                return Redirect::back()->with('error', "Anda berada di luar radius yang diizinkan. Jarak Anda " . round($jarak) . " meter dari kantor.");
            }
        }
        
        // ======================================================
        // VALIDASI WAKTU
        // ======================================================
        
        $jamMasukSetting = $settings['jam_masuk']->value ?? '08:00:00';
        $jamPulangSetting = $settings['jam_pulang']->value ?? '17:00:00';

        $now = Carbon::now();
        $today = $now->toDateString();
        $userId = Auth::id();

        $attendance = Attendance::where('user_id', $userId)
                                ->where('attendance_date', $today)
                                ->first();

        // LOGIKA ABSEN MASUK
        if (!$attendance) {
            $waktuMulaiAbsen = Carbon::parse($jamMasukSetting)->subMinutes(40);

            if ($now->isBefore($waktuMulaiAbsen)) {
                return Redirect::back()->with('error', "Belum waktunya absen masuk. Anda bisa absen mulai jam " . $waktuMulaiAbsen->format('H:i'));
            }

            if ($now->isAfter(Carbon::parse($jamPulangSetting))) {
                 return Redirect::back()->with('error', 'Waktu absen masuk sudah terlewat.');
            }

            Attendance::create([
                'user_id' => $userId,
                'attendance_date' => $today,
                'check_in_time' => $now->toTimeString(),
                'check_in_location' => $request->location,
            ]);

            return Redirect::back()->with('status', 'Absen masuk berhasil dicatat.');
        } 
        
        // LOGIKA ABSEN PULANG
        else {
            if ($attendance->check_out_time) {
                return Redirect::back()->with('error', 'Anda sudah melakukan absen pulang hari ini.');
            }
            
            if ($now->isBefore(Carbon::parse($jamPulangSetting))) {
                return Redirect::back()->with('error', "Belum waktunya absen pulang. Waktu pulang adalah jam " . Carbon::parse($jamPulangSetting)->format('H:i'));
            }

            $attendance->update([
                'check_out_time' => $now->toTimeString(),
                'check_out_location' => $request->location,
            ]);

            return Redirect::back()->with('status', 'Absen pulang berhasil dicatat.');
        }
    }
}