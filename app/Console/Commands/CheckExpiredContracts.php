<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class CheckExpiredContracts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'contracts:check-expired';

    /**
     * The console command description.
     */
    protected $description = 'Nonaktifkan otomatis user dengan status kontrak yang sudah melewati tanggal berakhir';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredUsers = User::where('employment_type', 'kontrak')
            ->where('is_active', true)
            ->whereNotNull('contract_end_date')
            ->whereDate('contract_end_date', '<', now())
            ->get();

        $count = 0;

        foreach ($expiredUsers as $user) {
            $user->update([
                'is_active' => false,
                'deactivated_reason' => 'Kontrak berakhir pada ' . $user->contract_end_date->format('d-m-Y'),
                'deactivated_at' => now(),
                'deactivated_by' => Auth::id(), // null kalau dijalankan via CLI/cron, terisi kalau via tombol manual
            ]);
            $count++;
        }

        $this->info("{$count} user dinonaktifkan karena kontrak berakhir.");

        return self::SUCCESS;
    }
}