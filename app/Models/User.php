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
        'employment_type',
        'contract_start_date',
        'contract_end_date',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
            'contract_start_date' => 'date',
            'contract_end_date' => 'date',
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

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * Relasi ke semua pengajuan lembur milik user ini.
     */
    public function overtimes(): HasMany
    {
        return $this->hasMany(Overtime::class);
    }

    public function deactivatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deactivated_by');
    }

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

    public function sisaHariKontrak(): ?int
    {
        if ($this->employment_type !== 'kontrak' || ! $this->contract_end_date) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->contract_end_date, false);
    }

    /**
     * Jumlah pengajuan cuti yang baru diproses atasan tapi belum dilihat user ini.
     */
    public function unreadLeavesCount(): int
    {
        return $this->leaves()->where('is_read', false)->count();
    }

    /**
     * Jumlah pengajuan lembur yang baru diproses atasan tapi belum dilihat user ini.
     */
    public function unreadOvertimesCount(): int
    {
        return $this->overtimes()->where('is_read', false)->count();
    }

    /**
     * Total notifikasi belum dibaca (cuti + lembur).
     */
    public function unreadNotificationsCount(): int
    {
        return $this->unreadLeavesCount() + $this->unreadOvertimesCount();
    }
}