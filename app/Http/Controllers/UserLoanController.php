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
        $books = Book::where('stock', '>=', 1)->get();
        return Inertia::render('User/Loans/Create', [
            'books' => $books
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        $book = Book::findOrFail($request->book_id);

        if($book->stock < 1){
            return back()->with('error', 'Buku habis');
        }

        Loan::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'status' => 'pending'
        ]);

        return redirect()->route('user.loans.index')->with('message','Peminjaman berhasil diajukan');
    }
}
