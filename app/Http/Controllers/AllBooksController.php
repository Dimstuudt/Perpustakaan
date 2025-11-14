<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AllBooksController extends Controller
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

        return Inertia::render('Public/AllBooks', [
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
}
