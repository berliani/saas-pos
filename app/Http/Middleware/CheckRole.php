<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        foreach ($roles as $role) {
            // Jika user memiliki salah satu peran yang diizinkan, lanjutkan
            if ($user->role === $role) {
                return $next($request);
            }
        }

        // Jika tidak memiliki peran yang sesuai, tolak akses
        abort(403, 'AKSES DITOLAK. ANDA TIDAK MEMILIKI HAK AKSES.');
    }
}
