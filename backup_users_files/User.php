<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'gaji_pokok',
        'tarif_lembur_per_jam',
        'leave_quota',
        'is_active',
        'deactivated_reason',
        'deactivated_at',
        'deactivated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAtasan(): bool
    {
        return $this->role === 'atasan';
    }

    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }

    /**
     * Relasi ke model Attendance. Satu user bisa punya banyak absensi.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Relasi ke semua pengajuan cuti/izin milik user ini.
     */
    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * Relasi ke admin yang menonaktifkan akun ini (kalau ada).
     */
    public function deactivatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }

    /**
     * Hitung sisa kuota cuti tahunan (tipe 'cuti' saja, tahun berjalan).
     * Dihitung dinamis dari data yang sudah disetujui, bukan counter tersimpan.
     */
    public function sisaKuotaCuti(): int
    {
        $terpakai = $this->leaves()
            ->where('type', 'cuti')
            ->where('status', 'approved')
            ->whereYear('start_date', Carbon::now()->year)
            ->get()
            ->sum(fn (Leave $leave) => $leave->total_days);

        return max(0, $this->leave_quota - $terpakai);
    }
}