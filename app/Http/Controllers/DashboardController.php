<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return match (true) {
            // =========================
            // SUPER ADMIN DASHBOARD
            // =========================
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
                        'total_loans' => $loan->total_loans,
                    ]),

                'chartData' => collect(range(5, 0))->map(function ($monthsAgo) {
                    $date = now()->subMonths($monthsAgo);
                    return [
                        'month' => $date->format('M'),
                        'loans' => Loan::whereYear('borrowed_at', $date->year)
                            ->whereMonth('borrowed_at', $date->month)
                            ->count(),
                    ];
                })->values(),

                'recentLoans' => Loan::with(['user:id,name', 'book:id,title'])
                    ->latest('borrowed_at')
                    ->take(10)
                    ->get()
                    ->map(fn($loan) => [
                        'id' => $loan->id,
                        'user' => $loan->user,
                        'book' => $loan->book,
                        'borrowed_at' => optional($loan->borrowed_at)?->format('d M Y'),
                        'due_date' => optional($loan->due_date)?->format('d M Y'),
                    ]),

                'overdueLoans' => Loan::whereNull('returned_at')
                    ->where('due_date', '<', now())
                    ->count(),
            ]),

            // =========================
            // ADMIN DASHBOARD
            // =========================
            $user->hasRole('Admin') => Inertia::render('Dashboard/Admin', [
                'stats' => [
                    'totalBooks'     => Book::count(),
                    'borrowedBooks'  => Loan::whereNull('returned_at')->count(),
                    'availableBooks' => Book::where('stock', '>', 0)->count(),
                    'overdueLoans'   => Loan::whereNull('returned_at')
                        ->where('due_date', '<', now())
                        ->count(),
                ],

                'activeLoans' => Loan::where('status', 'dipinjam') // <-- filter status
    ->whereNull('returned_at')                    // masih belum dikembalikan
    ->with(['user:id,name,email', 'book:id,title'])
    ->orderBy('due_date')
    ->take(10)
    ->get()
    ->map(fn($loan) => [
        'id' => $loan->id,
        'user' => $loan->user,
        'book' => $loan->book,
        'borrowed_at' => optional($loan->borrowed_at)?->format('d M Y'),
        'due_date' => optional($loan->due_date)?->format('d M Y'),
        'is_overdue' => optional($loan->due_date)?->isPast(),
        'days_overdue' => optional($loan->due_date)?->isPast()
            ? $loan->due_date->diffInDays(now())
            : 0,
    ]),

                'popularBooks' => Loan::select('book_id', DB::raw('COUNT(*) as total'))
                    ->groupBy('book_id')
                    ->orderByDesc('total')
                    ->take(5)
                    ->with('book:id,title,author')
                    ->get()
                    ->map(fn($loan) => [
                        'book' => $loan->book,
                        'count' => $loan->total,
                    ]),

                'chartData' => collect(range(2, 0))->map(function ($i) {
                    $date = now()->subMonths($i);
                    return [
                        'month' => $date->format('M'),
                        'count' => Loan::whereYear('borrowed_at', $date->year)
                            ->whereMonth('borrowed_at', $date->month)
                            ->count(),
                    ];
                }),
            ]),

          // =========================
// USER DASHBOARD
// =========================
default => Inertia::render('Dashboard/User', [
    'stats' => [
        'currentLoans' => Loan::where('user_id', $user->id)
            ->where('status', 'dipinjam') // hanya yang sedang dipinjam
            ->whereNull('returned_at')
            ->count(),
        'totalBorrowed' => Loan::where('user_id', $user->id)->count(),
        'overdueBooks' => Loan::where('user_id', $user->id)
            ->where('status', 'dipinjam')
            ->whereNull('returned_at')
            ->where('due_date', '<', now())
            ->count(),
        'totalFees' => Loan::where('user_id', $user->id)
            ->whereNotNull('returned_at')
            ->sum('fee'),
    ],

    'activeLoans' => Loan::where('user_id', $user->id)
        ->where('status', 'dipinjam') // filter aktif
        ->whereNull('returned_at')
        ->with('book:id,title,author,cover_path')
        ->orderBy('due_date')
        ->get()
        ->map(fn($loan) => [
            'id' => $loan->id,
            'book' => $loan->book,
            'borrowed_at' => $loan->borrowed_at ? $loan->borrowed_at->format('d M Y') : null,
            'due_date' => $loan->due_date ? $loan->due_date->format('d M Y') : null,
            'is_overdue' => $loan->due_date ? $loan->due_date->isPast() : false,
            'days_remaining' => $loan->due_date
                ? ($loan->due_date->isPast() ? 0 : now()->diffInDays($loan->due_date))
                : 0,
        ]),

    'loanHistory' => Loan::where('user_id', $user->id)
        ->whereNotNull('returned_at')
        ->with('book:id,title,author,cover_path')
        ->latest('returned_at')
        ->take(5)
        ->get()
        ->map(fn($loan) => [
            'id' => $loan->id,
            'book' => $loan->book,
            'borrowed_at' => $loan->borrowed_at ? $loan->borrowed_at->format('d M Y') : null,
            'returned_at' => $loan->returned_at ? $loan->returned_at->format('d M Y') : null,
            'fee' => $loan->fee,
        ]),

    'recommendedBooks' => Book::where('stock', '>', 0)
        ->inRandomOrder()
        ->take(6)
        ->get(['id', 'title', 'author', 'cover_path', 'stock']),

    'userStats' => [
        'joined' => $user->created_at ? $user->created_at->format('d M Y') : null,
        'daysSinceJoin' => $user->created_at ? now()->diffInDays($user->created_at) : 0,
        'avgLoanDuration' => round(
            Loan::where('user_id', $user->id)
                ->whereNotNull('returned_at')
                ->get()
                ->avg(fn($loan) => $loan->borrowed_at && $loan->returned_at
                    ? $loan->borrowed_at->diffInDays($loan->returned_at)
                    : 0
                ),
            1
        ),
    ],
]),
        };
    }
}
