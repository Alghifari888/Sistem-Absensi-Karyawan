<?php

use App\Models\AuditLog;
use App\Models\Leave;
use App\Models\Overtime;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('admin cannot edit or update admin user via user management', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $otherAdmin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get(route('admin.users.edit', $otherAdmin))->assertForbidden();
    $this->actingAs($admin)->put(route('admin.users.update', $otherAdmin), [
        'name' => 'Hacked',
        'email' => 'hacked@gmail.com',
        'role' => 'karyawan',
        'gaji_pokok' => 0,
        'tarif_lembur_per_jam' => 0,
        'leave_quota' => 12,
    ])->assertForbidden();
});

test('deactivated user is logged out on next authenticated request', function () {
    $user = User::factory()->create(['role' => 'karyawan', 'is_active' => true]);
    $user->update(['is_active' => false]);

    $response = $this->actingAs($user)->get(route('karyawan.dashboard'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

test('settings update validates input and rejects invalid values', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->post(route('admin.settings.update'), [
        'jam_masuk' => '08:00',
        'jam_pulang' => '17:00',
        'radius_absensi' => 5,
        'lokasi_kantor_lat' => 'not-a-lat',
        'lokasi_kantor_lon' => '106.8',
    ])->assertSessionHasErrors(['radius_absensi', 'lokasi_kantor_lat']);

    $this->actingAs($admin)->post(route('admin.settings.update'), [
        'jam_masuk' => '08:00',
        'jam_pulang' => '17:00',
        'radius_absensi' => 100,
        'lokasi_kantor_lat' => '-6.200000',
        'lokasi_kantor_lon' => '106.816666',
    ])->assertSessionHasNoErrors();
});

test('attendance fails closed without office coordinates and rejects invalid location format', function () {
    $karyawan = User::factory()->create(['role' => 'karyawan', 'is_active' => true]);
    Setting::create(['key' => 'qr_token_' . now()->toDateString(), 'value' => 'valid-token']);

    $this->actingAs($karyawan)->post(route('karyawan.attendance.store'), [
        'location' => '-6.200000,106.816666',
        'qr_token' => 'valid-token',
    ])->assertSessionHas('error');

    Setting::create(['key' => 'lokasi_kantor_lat', 'value' => '-6.200000']);
    Setting::create(['key' => 'lokasi_kantor_lon', 'value' => '106.816666']);
    Setting::create(['key' => 'radius_absensi', 'value' => '100']);

    $this->actingAs($karyawan)->post(route('karyawan.attendance.store'), [
        'location' => 'invalid-location',
        'qr_token' => 'valid-token',
    ])->assertSessionHasErrors(['location']);
});

test('leave proof is stored privately and only authorized users can view it', function () {
    Storage::fake('local');
    Storage::fake('public');

    $karyawan = User::factory()->create(['role' => 'karyawan', 'is_active' => true, 'leave_quota' => 12]);
    $otherKaryawan = User::factory()->create(['role' => 'karyawan', 'is_active' => true]);
    $atasan = User::factory()->create(['role' => 'atasan', 'is_active' => true]);

    $this->actingAs($karyawan)->post(route('karyawan.leaves.store'), [
        'type' => 'sakit',
        'start_date' => now()->toDateString(),
        'end_date' => now()->toDateString(),
        'reason' => 'Demam',
        'proof_document' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
    ])->assertSessionHasNoErrors();

    $leave = Leave::firstOrFail();
    Storage::disk('local')->assertExists($leave->proof_document);
    expect(Storage::disk('public')->allFiles())->toBeEmpty();

    \Illuminate\Support\Facades\Auth::logout();
    $this->get(route('leaves.proof', $leave))->assertRedirect(route('login'));
    $this->actingAs($otherKaryawan)->get(route('leaves.proof', $leave))->assertForbidden();
    $this->actingAs($karyawan)->get(route('leaves.proof', $leave))->assertOk();
    $this->actingAs($atasan)->get(route('leaves.proof', $leave))->assertOk();
});

test('atasan cannot re-process decided leave or overtime and cannot approve over-quota leave', function () {
    $karyawan = User::factory()->create(['role' => 'karyawan', 'is_active' => true, 'leave_quota' => 2]);
    $atasan = User::factory()->create(['role' => 'atasan', 'is_active' => true]);

    $leave1 = Leave::create([
        'user_id' => $karyawan->id,
        'type' => 'cuti',
        'start_date' => '2026-12-01',
        'end_date' => '2026-12-02',
        'reason' => 'Cuti 1',
        'status' => 'pending',
    ]);
    $leave2 = Leave::create([
        'user_id' => $karyawan->id,
        'type' => 'cuti',
        'start_date' => '2026-12-05',
        'end_date' => '2026-12-05',
        'reason' => 'Cuti 2',
        'status' => 'pending',
    ]);

    $this->actingAs($atasan)->put(route('atasan.leaves.update', $leave1), [
        'status' => 'approved',
    ])->assertSessionHas('status');

    // Re-updating already decided leave must be blocked
    $this->actingAs($atasan)->put(route('atasan.leaves.update', $leave1), [
        'status' => 'rejected',
    ])->assertSessionHas('error');
    expect($leave1->fresh()->status)->toBe('approved');

    // Second leave exceeds remaining quota (0) -> must be blocked
    $this->actingAs($atasan)->put(route('atasan.leaves.update', $leave2), [
        'status' => 'approved',
    ])->assertSessionHas('error');
    expect($leave2->fresh()->status)->toBe('pending');

    $overtime = Overtime::create([
        'user_id' => $karyawan->id,
        'overtime_date' => '2026-12-01',
        'start_time' => '18:00',
        'end_time' => '20:00',
        'reason' => 'Lembur',
        'status' => 'approved',
    ]);

    $this->actingAs($atasan)->put(route('atasan.overtime.update', $overtime), [
        'status' => 'rejected',
    ])->assertSessionHas('error');
    expect($overtime->fresh()->status)->toBe('approved');
});

test('daily qr token setting is not recorded in audit logs', function () {
    $countBefore = AuditLog::count();

    Setting::create(['key' => 'qr_token_' . now()->toDateString(), 'value' => 'secret-qr-token']);

    expect(AuditLog::count())->toBe($countBefore);
});
