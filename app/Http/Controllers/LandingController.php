<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Models\Loan;
use Inertia\Inertia;

class LandingController extends Controller
{
public function welcome()
{
    return Inertia::render('Public/Welcome', [
        'stats' => [
            'books'    => Book::count(),
            'members'  => User::count(),
            'articles' => 0,
            'loans'    => Loan::count(),
        ],
        'latestBooks' => Book::latest()->take(6)->get()->map(fn ($book) => [
            'id'          => $book->id,
            'title'       => $book->title,
            'author'      => $book->author,
            'cover_url'   => $book->cover_url,
            'description' => $book->description,
            'category'    => $book->category?->name,
        ]),
        // batasi kategori 6
        'categories' => Category::select('id', 'name')->take(6)->get(),
    ]);
}


public function preview($id)
{
    $book = Book::with('category')->findOrFail($id);

    $relatedBooks = Book::where('id', '!=', $book->id)
        ->where('category_id', $book->category_id)
        ->limit(6)
        ->get(['id', 'title', 'author', 'cover_path']); // ambil cover_path saja

    // mapping biar ada cover_url juga
    $relatedBooks->transform(function ($rb) {
        $rb->cover_url = $rb->cover_url; // otomatis dari accessor
        return $rb;
    });

    return Inertia::render('Public/Preview', [
        'book' => $book,
        'relatedBooks' => $relatedBooks,
    ]);
}



}
