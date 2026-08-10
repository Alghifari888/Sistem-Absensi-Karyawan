<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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
        $request->validate([
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
        ], [
            'email.regex' => 'Email wajib menggunakan domain @gmail.com.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.mixed' => 'Password wajib mengandung huruf besar, huruf kecil, angka, dan simbol.',
            'password.numbers' => 'Password wajib mengandung minimal 1 angka.',
            'password.symbols' => 'Password wajib mengandung minimal 1 simbol.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
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
        ], [
            'email.regex' => 'Email wajib menggunakan domain @gmail.com.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.mixed' => 'Password wajib mengandung huruf besar dan huruf kecil.',
            'password.numbers' => 'Password wajib mengandung minimal 1 angka.',
            'password.symbols' => 'Password wajib mengandung minimal 1 simbol.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('status', 'Data user berhasil diperbarui.');
    }

    /**
     * Mengaktifkan / menonaktifkan user (soft toggle, bukan hapus permanen).
     */
    public function toggleStatus(Request $request, User $user)
    {
        // Proteksi: tidak boleh menonaktifkan admin atau diri sendiri
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
}