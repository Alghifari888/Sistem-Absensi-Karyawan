<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Menampilkan daftar semua user
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Menampilkan form untuk edit user
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // Menyimpan perubahan data user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:karyawan,atasan',
            'gaji_pokok' => 'required|numeric|min:0',
            'tarif_lembur_per_jam' => 'required|numeric|min:0',
        ]);

        $user->update($request->all());

        return redirect()->route('admin.users.index')->with('status', 'Data user berhasil diperbarui.');
    }
}