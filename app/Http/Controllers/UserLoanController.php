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
        // ambil hanya buku fisik yang masih ada stok
        $books = Book::where('type', 'physical')
            ->where('stock', '>=', 0)
            ->select('id', 'title', 'author', 'stock')
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
            'status' => 'pending'
        ]);

        return redirect()->route('user.loans.index')
            ->with('message', 'Peminjaman berhasil diajukan');
    }

    public function status()
    {
        $loans = Loan::with('book')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('User/Loans/Status', [
            'loans' => $loans
        ]);
    }
}
