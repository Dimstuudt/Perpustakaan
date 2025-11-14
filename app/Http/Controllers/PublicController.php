<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Rack;
use App\Models\Cabinet;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function koleksi(Request $request)
    {
        // Level 1: Cabinet
        if (!$request->cabinet && !$request->rack) {
            $cabinets = Cabinet::withCount('racks')->get();

            // Tambahkan query latest books (exclude category "default")
            $latestBooks = Book::with('category')
                ->whereHas('category', function($query) {
                    $query->where('name', '!=', 'default');
                })
                ->orWhereDoesntHave('category') // Jika ada buku tanpa category
                ->latest()
                ->take(18)
                ->get()
                ->map(fn($book) => [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'description' => $book->description,
                    'cover_url' => $book->cover_url,
                    'category' => $book->category?->only(['id', 'name']),
                ]);

            return Inertia::render('Public/CabinetIndex', [
                'cabinets' => $cabinets,
                'latestBooks' => $latestBooks,
            ]);
        }

        // Level 2: Rack dalam Cabinet
        if ($request->cabinet && !$request->rack) {
            $cabinet = Cabinet::with('racks.books')->findOrFail($request->cabinet);
            $racks = $cabinet->racks()->withCount('books')->get();

            return Inertia::render('Public/RackIndex', [
                'cabinet' => [
                    'id' => $cabinet->id,
                    'name' => $cabinet->name,
                ],
                'racks' => $racks,
            ]);
        }

        // Level 3: Buku dalam Rack
        if ($request->rack) {
            $rack = Rack::with(['cabinet', 'books.category'])->findOrFail($request->rack);

            $books = Book::where('rack_id', $rack->id)
                ->with(['category', 'rack.cabinet'])
                ->latest()
                ->get()
                ->map(fn($book) => [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'publisher' => $book->publisher,
                    'description' => $book->description,
                    'cover_url' => $book->cover_url,
                    'category' => $book->category?->only(['id', 'name']),
                ]);

            $categories = Category::select('id', 'name')->get();

            return Inertia::render('Public/BooksIndex', [
                'rack' => [
                    'id' => $rack->id,
                    'name' => $rack->name,
                ],
                'cabinet' => [
                    'id' => $rack->cabinet->id,
                    'name' => $rack->cabinet->name,
                ],
                'books' => $books,
                'categories' => $categories,
            ]);
        }
    }
}
