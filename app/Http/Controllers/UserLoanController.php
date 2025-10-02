<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserLoanController extends Controller
{
public function index()
{
    // ambil semua buku fisik, termasuk stok 0
    $books = Book::where('type', 'physical')
        ->select('id', 'title', 'author', 'stock', 'fee')
        ->get();

    return Inertia::render('User/Loans/Create', [
        'books' => $books,
        'hasPendingLoan' => Loan::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists(),
    ]);
}



public function store(Request $request)
{
    $request->validate([
        'book_id' => 'required|exists:books,id'
    ]);

    // cek kalau user sudah ada peminjaman pending
    $alreadyLoan = Loan::where('user_id', auth()->id())
        ->where('status', 'pending')
        ->exists();

    if ($alreadyLoan) {
        return back()->with('error', 'Anda hanya bisa membuat 1 permohonan peminjaman.');
    }

    $book = Book::findOrFail($request->book_id);

    if ($book->stock < 1) {
        return back()->with('error', 'Buku habis.');
    }

    Loan::create([
        'user_id' => auth()->id(),
        'book_id' => $book->id,
        'status' => 'pending',
        'fee'    => $book->fee, // ✅ ambil harga dari buku
    ]);

    return redirect()->route('user.loans.index')
        ->with('message', 'Peminjaman berhasil diajukan dengan fee Rp ' . number_format($book->fee, 0, ',', '.'));
}


  public function status()
{
    $loans = Loan::with('book')
        ->where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(fn($loan) => [
            'id' => $loan->id,
            'status' => $loan->status,
            'borrowed_at' => $loan->borrowed_at?->format('Y-m-d H:i') ?? null,
            'due_date' => $loan->due_date?->format('Y-m-d H:i') ?? null, // ✅ baru
            'returned_at' => $loan->returned_at?->format('Y-m-d H:i') ?? null,
            'fee' => $loan->fee,
            'fine' => $loan->fine,                                         // ✅ baru
            'book' => [
                'title' => $loan->book->title,
            ],
        ]);

    return Inertia::render('User/Loans/Status', [
        'loans' => $loans
    ]);
}


   public function cancel($id)
{
    $loan = Loan::where('id', $id)
        ->where('status', 'pending')
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $loan->delete(); // langsung hapus

    return back()->with('success', 'Peminjaman berhasil dibatalkan.');
}


}
