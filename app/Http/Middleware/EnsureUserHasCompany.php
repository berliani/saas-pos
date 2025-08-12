<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        // Periksa apakah pengguna sudah login dan memiliki company_id
        if (Auth::check() && Auth::user()->company_id) {
            // Jika ya, lanjutkan permintaan
            return $next($request);
        }

        // Jika tidak, redirect ke halaman utama atau halaman pendaftaran perusahaan
        // dengan pesan peringatan.
        return redirect('/')->with('warning', 'Anda harus terdaftar dalam sebuah perusahaan untuk mengakses halaman ini.');
    }
}
