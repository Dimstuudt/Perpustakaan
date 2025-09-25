<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Loan;
use App\Models\Book;
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
                     'user' => [
                         'name' => $loan->user->name,
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
}
