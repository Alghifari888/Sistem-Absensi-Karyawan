<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
                'unique:'.User::class,
            ],
            'password' => [
                'required', 'confirmed',
                Rules\Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'role' => ['required', 'in:karyawan,atasan'],
            'employment_type' => ['nullable', 'required_if:role,karyawan', 'in:tetap,kontrak'],
            'contract_start_date' => ['nullable', 'required_if:employment_type,kontrak', 'date'],
            'contract_end_date' => ['nullable', 'required_if:employment_type,kontrak', 'date', 'after:contract_start_date'],
        ], [
            'email.regex' => 'Email wajib menggunakan domain @gmail.com.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.mixed' => 'Password wajib mengandung huruf besar, huruf kecil, angka, dan simbol.',
            'password.numbers' => 'Password wajib mengandung minimal 1 angka.',
            'password.symbols' => 'Password wajib mengandung minimal 1 simbol.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'employment_type.required_if' => 'Status kepegawaian wajib dipilih untuk role Karyawan.',
            'contract_start_date.required_if' => 'Tanggal mulai kontrak wajib diisi.',
            'contract_end_date.required_if' => 'Tanggal berakhir kontrak wajib diisi.',
            'contract_end_date.after' => 'Tanggal berakhir kontrak harus setelah tanggal mulai.',
        ]);

        // Field kontrak hanya relevan untuk role karyawan
        if ($validated['role'] !== 'karyawan') {
            $validated['employment_type'] = null;
            $validated['contract_start_date'] = null;
            $validated['contract_end_date'] = null;
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'employment_type' => $validated['employment_type'] ?? null,
            'contract_start_date' => $validated['contract_start_date'] ?? null,
            'contract_end_date' => $validated['contract_end_date'] ?? null,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        abort_if($user->isAdmin(), 403);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->isAdmin(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'email', 'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/',
                'unique:users,email,' . $user->id,
            ],
            'password' => [
                'nullable', 'confirmed',
                Rules\Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
            'role' => 'required|in:karyawan,atasan',
            'gaji_pokok' => 'required|numeric|min:0',
            'tarif_lembur_per_jam' => 'required|numeric|min:0',
            'leave_quota' => 'required|integer|min:0|max:365',
            'employment_type' => ['nullable', 'required_if:role,karyawan', 'in:tetap,kontrak'],
            'contract_start_date' => ['nullable', 'required_if:employment_type,kontrak', 'date'],
            'contract_end_date' => ['nullable', 'required_if:employment_type,kontrak', 'date', 'after:contract_start_date'],
        ], [
            'email.regex' => 'Email wajib menggunakan domain @gmail.com.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.mixed' => 'Password wajib mengandung huruf besar dan huruf kecil.',
            'password.numbers' => 'Password wajib mengandung minimal 1 angka.',
            'password.symbols' => 'Password wajib mengandung minimal 1 simbol.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'employment_type.required_if' => 'Status kepegawaian wajib dipilih untuk role Karyawan.',
            'contract_start_date.required_if' => 'Tanggal mulai kontrak wajib diisi.',
            'contract_end_date.required_if' => 'Tanggal berakhir kontrak wajib diisi.',
            'contract_end_date.after' => 'Tanggal berakhir kontrak harus setelah tanggal mulai.',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        // Field kontrak hanya relevan untuk role karyawan.
        // Catatan: kalau employment_type berubah dari 'kontrak' ke 'tetap',
        // tanggal kontrak lama SENGAJA TIDAK dihapus (disimpan sebagai riwayat).
        if ($validated['role'] !== 'karyawan') {
            $validated['employment_type'] = null;
            $validated['contract_start_date'] = null;
            $validated['contract_end_date'] = null;
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('status', 'Data user berhasil diperbarui.');
    }

    /**
     * Mengaktifkan / menonaktifkan user (soft toggle, bukan hapus permanen).
     */
    public function toggleStatus(Request $request, User $user)
    {
        if ($user->role === 'admin' || $user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak bisa mengubah status akun ini.');
        }

        if ($user->is_active) {
            $request->validate([
                'deactivated_reason' => 'required|string|max:255',
            ], [
                'deactivated_reason.required' => 'Alasan nonaktif wajib diisi.',
            ]);

            $user->update([
                'is_active' => false,
                'deactivated_reason' => $request->deactivated_reason,
                'deactivated_at' => now(),
                'deactivated_by' => Auth::id(),
            ]);

            return redirect()->route('admin.users.index')
                ->with('status', "User {$user->name} berhasil dinonaktifkan.");
        }

        $user->update([
            'is_active' => true,
            'deactivated_reason' => null,
            'deactivated_at' => null,
            'deactivated_by' => null,
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', "User {$user->name} berhasil diaktifkan kembali.");
    }

    /**
     * Trigger manual: cek & nonaktifkan otomatis user dengan kontrak yang sudah berakhir.
     */
    public function checkExpiredContracts()
    {
        Artisan::call('contracts:check-expired');
        $output = trim(Artisan::output());

        return redirect()->route('admin.users.index')
            ->with('status', $output ?: 'Pengecekan selesai. Tidak ada kontrak yang berakhir.');
    }
}