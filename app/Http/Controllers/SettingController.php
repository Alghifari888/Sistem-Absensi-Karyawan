<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan.
     */
    public function index()
    {
        // Ambil semua data settings dan ubah menjadi format yang mudah diakses di view
        $settings = Setting::all()->keyBy('key');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Menyimpan atau memperbarui data pengaturan.
     */
    public function update(Request $request)
    {
        // Daftar semua key yang diharapkan dari form
        $keys = [
            'jam_masuk',
            'jam_pulang',
            'radius_absensi',
            'lokasi_kantor_lat',
            'lokasi_kantor_lon',
        ];

        foreach ($keys as $key) {
            // updateOrCreate akan mencari berdasarkan 'key',
            // jika ada, akan di-update 'value'-nya.
            // jika tidak ada, akan dibuat record baru.
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key)]
            );
        }

        return Redirect::route('admin.settings.index')->with('status', 'Pengaturan berhasil diperbarui.');
    }
}