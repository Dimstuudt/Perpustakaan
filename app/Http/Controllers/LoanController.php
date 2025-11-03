<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;


class LoanController extends Controller
{
    // Admin lihat daftar peminjaman
public function index(Request $request)
{
    $query = Loan::with('user', 'book');

    // Filter berdasarkan status jika ada
    if ($request->has('status') && $request->status !== null) {
        $query->where('status', $request->status);
    }

    // Hitung statistik untuk semua data (tidak terpengaruh filter)
    $stats = [
        'pending' => Loan::where('status', 'pending')->count(),
        'dipinjam' => Loan::where('status', 'dipinjam')->count(),
        'dikembalikan' => Loan::where('status', 'dikembalikan')->count(),
        'ditolak' => Loan::where('status', 'ditolak')->count(),
    ];

    // Sorting berdasarkan prioritas status, kemudian created_at
    $loans = $query
        ->orderByRaw("
            CASE status
                WHEN 'pending' THEN 1
                WHEN 'dipinjam' THEN 2
                WHEN 'dikembalikan' THEN 3
                WHEN 'ditolak' THEN 4
            END
        ")
        ->orderBy('created_at', 'desc')
        ->paginate(6)
        ->through(fn($loan) => [
            'id' => $loan->id,
            'status' => $loan->status,
            'borrowed_at' => $loan->borrowed_at?->format('Y-m-d H:i') ?? null,
            'due_date' => $loan->due_date?->format('Y-m-d H:i') ?? null,
            'returned_at' => $loan->returned_at?->format('Y-m-d H:i') ?? null,
            'fee' => $loan->fee,
            'fine' => $loan->fine,
            'user' => [
                'username' => $loan->user->username,
            ],
            'book' => [
                'title' => $loan->book?->title ?? '-',
            ],
        ]);

    return Inertia::render('Admin/Loans/Index', [
        'loans' => $loans,
        'filters' => [
            'status' => $request->status
        ],
        'stats' => $stats
    ]);
}




// Approve peminjaman
public function approve(Loan $loan)
{
    if ($loan->status !== 'pending') {
        return back()->with('error', 'Peminjaman ini sudah diproses.');
    }

    if ($loan->book->stock <= 0) {
        return back()->with('error', 'Stok buku habis, tidak bisa disetujui.');
    }

    $loan->update([
        'status' => 'dipinjam',
        'borrowed_at' => Carbon::now(),
        'due_date' => Carbon::now()->addDays(7), // ✅ otomatis 7 hari ke depan
    ]);

    // kurangi stok
    $loan->book->decrement('stock');

    return back()->with('success', 'Peminjaman disetujui.');
}

    // Reject peminjaman
    public function reject(Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Peminjaman ini sudah diproses.');
        }

        $loan->update([
            'status' => 'ditolak',
        ]);

        return back()->with('success', 'Peminjaman ditolak.');
    }

    // Terima pengembalian
public function return(Loan $loan)
{
    if ($loan->status !== 'dipinjam') {
        return back()->with('error', 'Peminjaman ini belum dipinjam atau sudah dikembalikan.');
    }

    $now = Carbon::now();
    $fine = 0;

    // Pastikan due_date dalam format Carbon
    $dueDate = Carbon::parse($loan->due_date);

    // ✅ Logika yang lebih aman
    if ($now->gt($dueDate)) {
        $daysLate = $dueDate->diffInDays($now); // arah: due_date → now
        $fine = $daysLate * 1000; // 1000 per hari
    }

    $loan->update([
        'status' => 'dikembalikan',
        'returned_at' => $now,
        'fine' => $fine,
    ]);

    $loan->book->increment('stock');

    return back()->with('success', 'Pengembalian buku diterima. Denda: Rp ' . number_format($fine));
}


    // User create loan
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'book_id' => 'required|exists:books,id'
    //     ]);

    //     // cek pending
    //     $alreadyLoan = Loan::where('user_id', auth()->id())
    //         ->where('status', 'pending')
    //         ->exists();

    //     if ($alreadyLoan) {
    //         return back()->with('error', 'Anda hanya bisa membuat 1 permohonan peminjaman.');
    //     }

    //     $book = Book::findOrFail($request->book_id);

    //     if ($book->stock < 1) {
    //         return back()->with('error', 'Buku habis.');
    //     }

    //     // ✅ simpan fee dari book ke loan
    //     Loan::create([
    //         'user_id' => auth()->id(),
    //         'book_id' => $book->id,
    //         'status' => 'pending',
    //         'fee' => $book->fee,
    //     ]);

    //     return redirect()->route('user.loans.index')
    //         ->with('message', 'Peminjaman berhasil diajukan');
    // }
}
