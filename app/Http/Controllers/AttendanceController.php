<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\PayrollController;
use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function history(Request $request)
    {
        $bulan = max(1, min(12, (int) $request->input('bulan', now()->month)));
        $tahun = max(2000, min(2100, (int) $request->input('tahun', now()->year)));

        $jamMasukSetting = Setting::where('key', 'jam_masuk')->value('value') ?? '08:00:00';

        $attendances = Attendance::where('user_id', Auth::id())
            ->whereMonth('attendance_date', $bulan)
            ->whereYear('attendance_date', $tahun)
            ->orderBy('attendance_date', 'desc')
            ->get();

        $payroll = PayrollController::calculateMonthlyPayroll(Auth::user(), $bulan, $tahun);

        return view('karyawan.attendance.index', compact('attendances', 'bulan', 'tahun', 'jamMasukSetting', 'payroll'));
    }

    public function payslip(Request $request)
    {
        $bulan = max(1, min(12, (int) $request->input('bulan', now()->month)));
        $tahun = max(2000, min(2100, (int) $request->input('tahun', now()->year)));

        return PayrollController::downloadPayslipForUser(Auth::user(), $bulan, $tahun);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $request->validate([
            'location' => ['required', 'string', 'regex:/^-?\d+(\.\d+)?,-?\d+(\.\d+)?$/'],
            'qr_token' => 'required|string',
        ]);

        $now = Carbon::now();
        $today = $now->toDateString();
        $userId = Auth::id();

        // 2. Validasi Token QR Code
        $qrTokenKey = 'qr_token_' . $today;
        $validToken = Setting::where('key', $qrTokenKey)->value('value');

        if (!$validToken || $request->qr_token !== $validToken) {
            return Redirect::back()->with('error', 'QR Code tidak valid atau sudah kedaluwarsa.');
        }

        // Ambil semua pengaturan sekaligus untuk efisiensi
        $settings = Setting::all()->keyBy('key');

        // 3. Validasi Jarak Lokasi (GPS) — fail-closed jika koordinat kantor belum disetel
        $lokasiKantorLat = $settings['lokasi_kantor_lat']->value ?? null;
        $lokasiKantorLon = $settings['lokasi_kantor_lon']->value ?? null;
        $radiusAbsensi = (int) ($settings['radius_absensi']->value ?? 100);

        if (!is_numeric($lokasiKantorLat) || !is_numeric($lokasiKantorLon)) {
            return Redirect::back()->with('error', 'Lokasi kantor belum diatur oleh admin. Silakan hubungi admin.');
        }

        [$latitude, $longitude] = array_map('floatval', explode(',', $request->location));
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return Redirect::back()->with('error', 'Koordinat lokasi tidak valid.');
        }

        $jarak = calculateDistance($latitude, $longitude, (float) $lokasiKantorLat, (float) $lokasiKantorLon);

        if ($jarak > $radiusAbsensi) {
            return Redirect::back()->with('error', "Anda berada di luar radius. Jarak Anda " . round($jarak) . " meter dari kantor.");
        }
        
        // 4. Validasi Waktu & Proses Absensi
        $jamMasukSetting = $settings['jam_masuk']->value ?? '08:00:00';
        $jamPulangSetting = $settings['jam_pulang']->value ?? '17:00:00';

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
