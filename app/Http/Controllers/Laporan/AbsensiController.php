<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Tetapkan bulan dan tahun, jika tidak ada input, gunakan bulan dan tahun saat ini
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);

        // Ambil pengaturan jam masuk untuk cek keterlambatan
        $jamMasukSetting = Setting::where('key', 'jam_masuk')->value('value') ?? '08:00:00';

        // Ambil semua user dengan role karyawan
        // Beserta data absensinya pada bulan dan tahun yang dipilih
        $karyawan = User::where('role', 'karyawan')
            ->with(['attendances' => function ($query) use ($bulan, $tahun) {
                $query->whereMonth('attendance_date', $bulan)
                      ->whereYear('attendance_date', $tahun);
            }])
            ->get();

        // Kirim data ke view
        return view('laporan.absensi.index', compact('karyawan', 'bulan', 'tahun', 'jamMasukSetting'));
    }
}