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
        'latestBooks' => Book::with('category')
            ->whereHas('category', function($query) {
                $query->where('name', '!=', 'default');
            })
            ->orWhereDoesntHave('category')
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($book) => [
                'id'          => $book->id,
                'title'       => $book->title,
                'author'      => $book->author,
                'cover_url'   => $book->cover_url,
                'description' => $book->description,
                'category'    => $book->category?->name,
            ]),
        // batasi kategori 6 dan exclude "default"
        'categories' => Category::select('id', 'name')
            ->where('name', '!=', 'default')
            ->take(6)
            ->get(),
    ]);
}

public function preview($id)
{
    $book = Book::with('category')->findOrFail($id);

 $relatedBooks = Book::where('id', '!=', $book->id)
    ->where('category_id', $book->category_id)
    ->inRandomOrder() // 🔥 ambil urutan acak
    ->limit(5)
    ->get(['id', 'title', 'author', 'cover_path']);


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
