<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
   public function toResponse($request)
    {
        $user = auth()->user();

        // Tentukan URL tujuan berdasarkan peran (role)
        if ($user->role === 'owner') {
            $redirectUrl = route('dashboard');
        } elseif ($user->role === 'staff') {
            // Pastikan ini mengarah ke rute dashboard staff
            $redirectUrl = route('staff.dashboard');
        } else {
            // Fallback jika peran tidak terdefinisi
            $redirectUrl = config('fortify.home');
        }

        return $request->wantsJson()
                    ? new JsonResponse(['two_factor' => false])
                    : redirect()->intended($redirectUrl);
    }
}
