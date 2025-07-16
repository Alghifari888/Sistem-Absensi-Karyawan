<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Overtime;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    /**
     * Menampilkan halaman utama penggajian dengan filter.
     */
    public function index(Request $request)
    {
        // PERBAIKAN: Konversi input bulan dan tahun menjadi angka (integer)
        $bulan = (int)$request->input('bulan', now()->month);
        $tahun = (int)$request->input('tahun', now()->year);

        $jamMasukSetting = \App\Models\Setting::where('key', 'jam_masuk')->value('value') ?? '08:00:00';

        $employees = User::where('role', 'karyawan')->get();
        $payrollData = [];

        foreach ($employees as $employee) {
            $totalOvertimeHours = Overtime::where('user_id', $employee->id)
                ->where('status', 'approved')
                ->whereMonth('overtime_date', $bulan)
                ->whereYear('overtime_date', $tahun)
                ->get()
                ->reduce(function ($carry, $item) {
                    $start = Carbon::parse($item->start_time);
                    $end = Carbon::parse($item->end_time);
                    return $carry + $end->diffInHours($start);
                }, 0);
            
            $totalAttendanceDays = Attendance::where('user_id', $employee->id)
                ->whereMonth('attendance_date', $bulan)
                ->whereYear('attendance_date', $tahun)
                ->count();

            $gajiPokok = $employee->gaji_pokok;
            $upahLembur = $totalOvertimeHours * $employee->tarif_lembur_per_jam;
            $gajiTotal = $gajiPokok + $upahLembur;

            $payrollData[] = [
                'user' => $employee,
                'total_kehadiran' => $totalAttendanceDays,
                'total_jam_lembur' => $totalOvertimeHours,
                'gaji_pokok' => $gajiPokok,
                'upah_lembur' => $upahLembur,
                'gaji_total' => $gajiTotal,
            ];
        }

        return view('admin.payroll.index', compact('payrollData', 'bulan', 'tahun'));
    }

    /**
     * Membuat dan men-download slip gaji dalam format PDF.
     */
    public function generatePayslip(Request $request, User $user)
    {
        // PERBAIKAN: Konversi input bulan dan tahun menjadi angka (integer)
        $bulan = (int)$request->input('bulan', now()->month);
        $tahun = (int)$request->input('tahun', now()->year);

        $totalOvertimeHours = Overtime::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereMonth('overtime_date', $bulan)
            ->whereYear('overtime_date', $tahun)
            ->get()
            ->reduce(function ($carry, $item) {
                $start = Carbon::parse($item->start_time);
                $end = Carbon::parse($item->end_time);
                return $carry + $end->diffInHours($start);
            }, 0);
        
        $totalAttendanceDays = Attendance::where('user_id', $user->id)
            ->whereMonth('attendance_date', $bulan)
            ->whereYear('attendance_date', $tahun)
            ->count();

        $gajiPokok = $user->gaji_pokok;
        $upahLembur = $totalOvertimeHours * $user->tarif_lembur_per_jam;
        $gajiTotal = $gajiPokok + $upahLembur;

        $data = [
            'user' => $user,
            'bulan' => \Carbon\Carbon::create()->month($bulan)->translatedFormat('F'),
            'tahun' => $tahun,
            'total_kehadiran' => $totalAttendanceDays,
            'total_jam_lembur' => $totalOvertimeHours,
            'gaji_pokok' => $gajiPokok,
            'upah_lembur' => $upahLembur,
            'gaji_total' => $gajiTotal,
        ];
        
        $pdf = Pdf::loadView('admin.payroll.payslip', $data);

        return $pdf->download('slip-gaji-' . $user->name . '-' . $bulan . '-' . $tahun . '.pdf');
    }
}
