<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserLoanController extends Controller
{
 public function index(Request $request)
    {
        // Start query
        $query = Book::with('category');

        // FILTER OPTIONAL - Search (title, author, ISBN)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // FILTER OPTIONAL - Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // FILTER OPTIONAL - Year (pakai kolom "year" integer)
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // FILTER OPTIONAL - Publisher
        if ($request->filled('publisher')) {
            $query->where('publisher', 'like', "%{$request->publisher}%");
        }

        // EXCLUDE CATEGORY "default"
        $query->where(function ($q) {
            $q->whereHas('category', function($cat) {
                $cat->where('name', '!=', 'default');
            })
            ->orWhereDoesntHave('category'); // buku tanpa category juga ditampilkan
        });

        // SORTING
        switch ($request->sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'author_asc':
                $query->orderBy('author', 'asc');
                break;
            case 'author_desc':
                $query->orderBy('author', 'desc');
                break;
            default: // newest
                $query->latest();
        }

        // Paginate
        $books = $query->paginate(24)->withQueryString();

        // Filter options
        $categories = Category::select('id', 'name')
            ->where('name', '!=', 'default')
            ->get();

        $publishers = Book::select('publisher')
            ->distinct()
            ->whereNotNull('publisher')
            ->pluck('publisher')
            ->sort()
            ->values();

        // Dropdown tahun (pakai kolom "year" integer)
        $years = Book::select('year')
            ->distinct()
            ->whereNotNull('year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return Inertia::render('User/Loans/Create', [
            'books'      => $books,
            'categories' => $categories,
            'publishers' => $publishers,
            'years'      => $years,
            'filters'    => [
                'search'      => $request->search,
                'category_id' => $request->category_id,
                'year'        => $request->year,
                'publisher'   => $request->publisher,
                'sort'        => $request->sort ?? 'newest',
            ],
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



public function show($id)
{
    $book = Book::with(['category', 'rack', 'loans'])->findOrFail($id);

    return Inertia::render('User/Loans/Show', [
        'book' => $book,
        'hasPendingLoan' => Loan::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists(),
    ]);
}


}
