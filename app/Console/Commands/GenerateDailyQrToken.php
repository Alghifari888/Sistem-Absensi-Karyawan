<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use Illuminate\Support\Str;

class GenerateDailyQrToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-daily-qr-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new QR code token for the current day';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();
        $qrTokenKey = 'qr_token_' . $today;

        // Logika ini sama persis seperti di QrCodeController
        // Gunakan updateOrCreate untuk efisiensi. Jika sudah ada, update. Jika belum, buat.
        Setting::updateOrCreate(
            ['key' => $qrTokenKey],
            ['value' => Str::random(40)]
        );

        $this->info('Daily QR token has been generated successfully.');
        return 0;
    }
}