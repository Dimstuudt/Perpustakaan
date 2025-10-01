<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return match (true) {
          // SUPER ADMIN DASHBOARD
$user->hasRole('Super Admin') => Inertia::render('Dashboard/SuperAdmin', [
    'message' => 'Selamat datang Super Admin 👑',
    'stats' => [
        'totalBooks'     => Book::count(),
        'borrowedBooks'  => Loan::whereNull('returned_at')->count(),
        'totalUsers'     => User::count(),
        'totalFee'       => Loan::whereNotNull('returned_at')->sum('fee'),
    ],
    'topBorrowers' => Loan::select('user_id', DB::raw('COUNT(*) as total_loans'))
        ->groupBy('user_id')
        ->orderByDesc('total_loans')
        ->take(5)
        ->with('user:id,name,email')
        ->get(),

    // ✅ tren pinjaman (6 bulan terakhir)
    'chartData' => Loan::select(
            DB::raw("strftime('%Y-%m', borrowed_at) as month"),
            DB::raw('COUNT(*) as loans')
        )
        ->where('borrowed_at', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get(),
]),

            // ADMIN DASHBOARD
            $user->hasRole('Admin') => Inertia::render('Dashboard/Admin', [
                'message' => 'Selamat datang Admin 🚀',
            ]),

            // USER DASHBOARD
            default => Inertia::render('Dashboard/User', [
                'message' => 'Selamat datang 🙌',
            ]),
        };
    }
}
