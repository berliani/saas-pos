<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaffDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard khusus untuk staff.
     */
    public function index()
    {
        return view('staff.dashboard');
    }
}
