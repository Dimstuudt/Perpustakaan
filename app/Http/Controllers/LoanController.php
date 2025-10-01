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
  public function index()
{
    $loans = Loan::with('user', 'book')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(fn($loan) => [
            'id' => $loan->id,
            'status' => $loan->status,
            'borrowed_at' => $loan->borrowed_at?->format('Y-m-d H:i') ?? null,
            'returned_at' => $loan->returned_at?->format('Y-m-d H:i') ?? null,
            'fee' => $loan->fee,
            'user' => [
                // ✅ ambil username bukan name
                'username' => $loan->user->username,
            ],
            'book' => [
                'title' => $loan->book->title,
            ],
        ]);

    return Inertia::render('Admin/Loans/Index', [
        'loans' => $loans
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

        $loan->update([
            'status' => 'dikembalikan',
            'returned_at' => Carbon::now(),
        ]);

        // tambah stok
        $loan->book->increment('stock');

        return back()->with('success', 'Pengembalian buku diterima.');
    }

    // User create loan
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        // cek pending
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

        // ✅ simpan fee dari book ke loan
        Loan::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'status' => 'pending',
            'fee' => $book->fee,
        ]);

        return redirect()->route('user.loans.index')
            ->with('message', 'Peminjaman berhasil diajukan');
    }
}
