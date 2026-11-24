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
        $bulan = max(1, min(12, (int) $request->input('bulan', now()->month)));
        $tahun = max(2000, min(2100, (int) $request->input('tahun', now()->year)));

        $employees = User::where('role', 'karyawan')->get();
        $payrollData = $employees
            ->map(fn (User $employee) => self::calculateMonthlyPayroll($employee, $bulan, $tahun))
            ->all();

        return view('admin.payroll.index', compact('payrollData', 'bulan', 'tahun'));
    }

    /**
     * Membuat dan men-download slip gaji dalam format PDF.
     */
    public function generatePayslip(Request $request, User $user)
    {
        $bulan = max(1, min(12, (int) $request->input('bulan', now()->month)));
        $tahun = max(2000, min(2100, (int) $request->input('tahun', now()->year)));

        return self::downloadPayslipForUser($user, $bulan, $tahun);
    }

    public static function calculateMonthlyPayroll(User $user, int $bulan, int $tahun): array
    {
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

        $gajiPokok = $user->gaji_pokok ?? 0;
        $upahLembur = $totalOvertimeHours * ($user->tarif_lembur_per_jam ?? 0);

        return [
            'user' => $user,
            'total_kehadiran' => $totalAttendanceDays,
            'total_jam_lembur' => $totalOvertimeHours,
            'gaji_pokok' => $gajiPokok,
            'upah_lembur' => $upahLembur,
            'gaji_total' => $gajiPokok + $upahLembur,
        ];
    }

    public static function downloadPayslipForUser(User $user, int $bulan, int $tahun)
    {
        $payroll = self::calculateMonthlyPayroll($user, $bulan, $tahun);
        $payroll['bulan'] = Carbon::create()->month($bulan)->translatedFormat('F');
        $payroll['tahun'] = $tahun;

        $pdf = Pdf::loadView('admin.payroll.payslip', $payroll);
        $safeName = \Illuminate\Support\Str::slug($user->name) ?: 'karyawan';

        return $pdf->download("slip-gaji-{$safeName}-{$bulan}-{$tahun}.pdf");
    }
}
