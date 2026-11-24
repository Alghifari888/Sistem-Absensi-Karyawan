<?php

use App\Models\Attendance;
use App\Models\User;

test('karyawan can view attendance history and download own payslip pdf', function () {
    $karyawan = User::factory()->create([
        'role' => 'karyawan',
        'is_active' => true,
        'gaji_pokok' => 5000000,
        'tarif_lembur_per_jam' => 50000,
    ]);
    $atasan = User::factory()->create(['role' => 'atasan', 'is_active' => true]);

    Attendance::create([
        'user_id' => $karyawan->id,
        'attendance_date' => now()->toDateString(),
        'check_in_time' => '07:55:00',
        'check_in_location' => '-6.2,106.8',
    ]);

    $this->actingAs($karyawan)
        ->get(route('karyawan.attendance.index'))
        ->assertOk()
        ->assertSee('Tepat Waktu')
        ->assertSee('5.000.000');

    $pdfResponse = $this->actingAs($karyawan)
        ->get(route('karyawan.payslip', ['bulan' => now()->month, 'tahun' => now()->year]));

    $pdfResponse->assertOk();
    expect($pdfResponse->headers->get('content-type'))->toContain('application/pdf');

    $this->actingAs($atasan)->get(route('karyawan.attendance.index'))->assertForbidden();
    $this->actingAs($atasan)->get(route('karyawan.payslip'))->assertForbidden();
});

test('admin dashboard shows expiring contracts within 30 days only', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    User::factory()->create([
        'name' => 'Kontrak Segera Habis',
        'role' => 'karyawan',
        'is_active' => true,
        'employment_type' => 'kontrak',
        'contract_start_date' => now()->subMonths(6)->toDateString(),
        'contract_end_date' => now()->addDays(10)->toDateString(),
    ]);

    User::factory()->create([
        'name' => 'Kontrak Masih Lama',
        'role' => 'karyawan',
        'is_active' => true,
        'employment_type' => 'kontrak',
        'contract_start_date' => now()->subMonth()->toDateString(),
        'contract_end_date' => now()->addDays(60)->toDateString(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Peringatan Kontrak Hampir Habis')
        ->assertSee('Kontrak Segera Habis')
        ->assertDontSee('Kontrak Masih Lama');
});
