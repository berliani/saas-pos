<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil hanya user dari perusahaan yang sama dengan owner yang sedang login
        $users = auth()->user()->company->users()->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => auth()->user()->company_id, // Set company_id secara otomatis
            'role' => 'staff', // Semua user yang dibuat owner adalah staff
        ]);

        return redirect()->route('users.index')->with('success', 'Staff baru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Keamanan: Pastikan owner hanya bisa mengedit staff di perusahaannya sendiri
        // dan tidak bisa mengedit dirinya sendiri melalui URL ini.
        if ($user->company_id !== auth()->user()->company_id || $user->isOwner()) {
            abort(403, 'AKSES DITOLAK');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Keamanan: Pastikan user yang akan diupdate adalah staff di perusahaan yang sama
        if ($user->company_id !== auth()->user()->company_id || $user->isOwner()) {
            abort(403, 'AKSES DITOLAK');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('users.index')->with('success', 'Data staff berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        
        if ($user->company_id !== auth()->user()->company_id || $user->isOwner()) {
            abort(403, 'AKSES DITOLAK');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Akun staff berhasil dihapus.');
    }
}
