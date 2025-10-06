<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PublicController extends Controller
{
 public function koleksi(Request $request)
{
    $query = Book::query()->with('category');

    // Filter kategori jika ada
    if ($request->category) {
        $query->whereHas('category', fn($q) => $q->where('id', $request->category));
    }

    // Ambil data buku dengan pagination (misal 12 per halaman)
    $books = $query->latest()->paginate(12)->withQueryString()->through(fn($book) => [
        'id' => $book->id,
        'title' => $book->title,
        'author' => $book->author,
        'publisher' => $book->publisher,  // ✅ tambahkan publisher
        'description' => $book->description,
        'cover_url' => $book->cover_url,
        'category' => $book->category ? ['id' => $book->category->id, 'name' => $book->category->name] : null,
    ]);

    $categories = Category::select('id', 'name')->get();

    return Inertia::render('Public/BooksIndex', [
        'books' => $books,
        'categories' => $categories,
        'currentCategory' => $request->category ? (int)$request->category : null,
    ]);
}

}
