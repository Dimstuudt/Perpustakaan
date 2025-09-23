<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return match (true) {
            $user->hasRole('Super Admin') => Inertia::render('Dashboard/SuperAdmin')->with('message', 'Selamat datang Super Admin 👑'),
            $user->hasRole('Admin')       => Inertia::render('Dashboard/Admin')->with('message', 'Selamat datang Admin 🚀'),
            default                       => Inertia::render('Dashboard/User')->with('message', 'Selamat datang 🙌'),
        };
    }
}
