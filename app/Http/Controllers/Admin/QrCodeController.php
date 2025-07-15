<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QrCodeController extends Controller
{
    public function show()
    {
        // Membuat atau mendapatkan token unik untuk hari ini
        $today = now()->toDateString();
        $qrTokenKey = 'qr_token_' . $today;

        // Cari token di pengaturan, jika tidak ada, buat yang baru
        $setting = Setting::firstOrCreate(
            ['key' => $qrTokenKey],
            ['value' => Str::random(40)]
        );

        $qrToken = $setting->value;

        return view('admin.qrcode.show', compact('qrToken'));
    }
}