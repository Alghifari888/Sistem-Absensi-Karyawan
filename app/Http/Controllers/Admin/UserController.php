<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user.
     */
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat user baru.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Menyimpan user baru ke database.
     */
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
            'password' => $request->password, // otomatis di-hash oleh cast 'hashed' di Model User
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk edit user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Menyimpan perubahan data user.
     */
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
        ], [
            'email.regex' => 'Email wajib menggunakan domain @gmail.com.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.mixed' => 'Password wajib mengandung huruf besar dan huruf kecil.',
            'password.numbers' => 'Password wajib mengandung minimal 1 angka.',
            'password.symbols' => 'Password wajib mengandung minimal 1 simbol.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Jika field password dikosongkan, jangan ikut di-update
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('status', 'Data user berhasil diperbarui.');
    }
}