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
                    ->get()
                    ->map(fn($loan) => [
                        'user' => $loan->user,
                        'total_loans' => $loan->total_loans
                    ]),

                // Tren peminjaman 6 bulan terakhir dengan nama bulan
                'chartData' => collect(range(5, 0))->map(function($monthsAgo) {
                    $date = now()->subMonths($monthsAgo);
                    $count = Loan::whereYear('borrowed_at', $date->year)
                        ->whereMonth('borrowed_at', $date->month)
                        ->count();

                    return [
                        'month' => $date->format('M'),
                        'loans' => $count
                    ];
                })->values(),

                // Data tambahan untuk Super Admin
                'recentLoans' => Loan::with(['user:id,name', 'book:id,title'])
                    ->latest('borrowed_at')
                    ->take(10)
                    ->get(),

                'overdueLoans' => Loan::whereNull('returned_at')
                    ->where('due_date', '<', now())
                    ->count(),
            ]),

            // ADMIN DASHBOARD
            $user->hasRole('Admin') => Inertia::render('Dashboard/Admin', [
                'stats' => [
                    'totalBooks'     => Book::count(),
                    'borrowedBooks'  => Loan::whereNull('returned_at')->count(),
                    'availableBooks' => Book::where('stock', '>', 0)->count(),
                    'overdueLoans'   => Loan::whereNull('returned_at')
                        ->where('due_date', '<', now())
                        ->count(),
                ],

                // Peminjaman yang perlu diproses
                'pendingReturns' => Loan::whereNull('returned_at')
                    ->with(['user:id,name,email', 'book:id,title'])
                    ->orderBy('due_date', 'asc')
                    ->take(10)
                    ->get()
                    ->map(fn($loan) => [
                        'id' => $loan->id,
                        'user' => $loan->user,
                        'book' => $loan->book,
                        'borrowed_at' => $loan->borrowed_at,
                        'due_date' => $loan->due_date,
                        'is_overdue' => Carbon::parse($loan->due_date)->isPast(),
                        'days_overdue' => Carbon::parse($loan->due_date)->isPast()
                            ? Carbon::parse($loan->due_date)->diffInDays(now())
                            : 0,
                    ]),

                // Buku yang paling sering dipinjam
                'popularBooks' => Loan::select('book_id', DB::raw('COUNT(*) as loan_count'))
                    ->groupBy('book_id')
                    ->orderByDesc('loan_count')
                    ->take(5)
                    ->with('book:id,title,author')
                    ->get()
                    ->map(fn($loan) => [
                        'book' => $loan->book,
                        'loan_count' => $loan->loan_count
                    ]),

                // Statistik bulanan
                'monthlyStats' => [
                    'thisMonth' => Loan::whereMonth('borrowed_at', now()->month)
                        ->whereYear('borrowed_at', now()->year)
                        ->count(),
                    'lastMonth' => Loan::whereMonth('borrowed_at', now()->subMonth()->month)
                        ->whereYear('borrowed_at', now()->subMonth()->year)
                        ->count(),
                ],

                // Tren peminjaman 3 bulan terakhir
                'chartData' => collect(range(2, 0))->map(function($monthsAgo) {
                    $date = now()->subMonths($monthsAgo);
                    $count = Loan::whereYear('borrowed_at', $date->year)
                        ->whereMonth('borrowed_at', $date->month)
                        ->count();

                    return [
                        'month' => $date->format('M'),
                        'loans' => $count
                    ];
                })->values(),
            ]),

            // USER DASHBOARD
            default => Inertia::render('Dashboard/User', [
                'stats' => [
                    'currentLoans'   => Loan::where('user_id', $user->id)
                        ->whereNull('returned_at')
                        ->count(),
                    'totalBorrowed'  => Loan::where('user_id', $user->id)->count(),
                    'overdueBooks'   => Loan::where('user_id', $user->id)
                        ->whereNull('returned_at')
                        ->where('due_date', '<', now())
                        ->count(),
                    'totalFees'      => Loan::where('user_id', $user->id)
                        ->whereNotNull('returned_at')
                        ->sum('fee'),
                ],

                // Buku yang sedang dipinjam
                'activeLoans' => Loan::where('user_id', $user->id)
                    ->whereNull('returned_at')
                    ->with('book:id,title,author,cover_image')
                    ->orderBy('due_date', 'asc')
                    ->get()
                    ->map(fn($loan) => [
                        'id' => $loan->id,
                        'book' => $loan->book,
                        'borrowed_at' => $loan->borrowed_at,
                        'due_date' => $loan->due_date,
                        'is_overdue' => Carbon::parse($loan->due_date)->isPast(),
                        'days_remaining' => Carbon::parse($loan->due_date)->isPast()
                            ? 0
                            : now()->diffInDays(Carbon::parse($loan->due_date)),
                    ]),

                // Riwayat peminjaman (5 terakhir)
                'loanHistory' => Loan::where('user_id', $user->id)
                    ->whereNotNull('returned_at')
                    ->with('book:id,title,author,cover_image')
                    ->latest('returned_at')
                    ->take(5)
                    ->get()
                    ->map(fn($loan) => [
                        'id' => $loan->id,
                        'book' => $loan->book,
                        'borrowed_at' => $loan->borrowed_at,
                        'returned_at' => $loan->returned_at,
                        'fee' => $loan->fee,
                    ]),

                // Rekomendasi buku (buku yang tersedia dengan stok > 0)
                'recommendedBooks' => Book::where('stock', '>', 0)
                    ->inRandomOrder()
                    ->take(6)
                    ->get(['id', 'title', 'author', 'cover_image', 'stock']),

                // Statistik user
                'userStats' => [
                    'joinDate' => $user->created_at,
                    'totalDays' => now()->diffInDays($user->created_at),
                    'averageLoanDays' => Loan::where('user_id', $user->id)
                        ->whereNotNull('returned_at')
                        ->get()
                        ->avg(function($loan) {
                            return Carbon::parse($loan->borrowed_at)
                                ->diffInDays(Carbon::parse($loan->returned_at));
                        }) ?? 0,
                ],
            ]),
        };
    }
}
