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
        $validated = $request->validate([
            'jam_masuk' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/'],
            'jam_pulang' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', 'after:jam_masuk'],
            'radius_absensi' => ['required', 'integer', 'min:10', 'max:50000'],
            'lokasi_kantor_lat' => ['required', 'numeric', 'between:-90,90'],
            'lokasi_kantor_lon' => ['required', 'numeric', 'between:-180,180'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return Redirect::route('admin.settings.index')->with('status', 'Pengaturan berhasil diperbarui.');
    }
}