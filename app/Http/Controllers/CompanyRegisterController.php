<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CompanyRegisterController extends Controller
{
    public function show()
    {
        return view('auth.register-company');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:100',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        DB::beginTransaction();

        try {
            // 1. Membuat data perusahaan
            $company = Company::create([
                'name' => $request->company_name,
                'address' => $request->address,
                'phone' => $request->phone,
                // 'website' bisa ditambahkan jika ada di form
            ]);

            // 2. Membuat data user sebagai owner perusahaan tersebut
            $user = User::create([
                'company_id' => $company->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'owner',
            ]);

            DB::commit();

            // 3. Langsung login sebagai user yang baru dibuat
            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Perusahaan berhasil terdaftar dan Anda masuk sebagai owner.');

        } catch (\Exception $e) {
            DB::rollBack();

            // Redirect kembali dengan pesan error
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat pendaftaran. Silakan coba lagi.']);
        }
    }
}
