<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BookController extends Controller
{
    // Menampilkan daftar buku
    public function index()
    {
        $books = Book::with('category')->latest()->paginate(10);

        return Inertia::render('Books/Index', [
            'books' => $books->through(fn($book) => [
                'id'          => $book->id,
                'isbn'        => $book->isbn,
                'title'       => $book->title,
                'author'      => $book->author,
                'publisher'   => $book->publisher,
                'year'        => $book->year,
                'pages'       => $book->pages,
                'category'    => $book->category ? $book->category->name : null,
                'description' => $book->description,
            ]),
        ]);
    }

    // Menampilkan form tambah buku
    public function create()
    {
        $categories = Category::all(); // Supaya bisa dipilih saat tambah buku

        return Inertia::render('Books/Create', [
            'categories' => $categories,
        ]);
    }

    // Simpan buku baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn'        => 'required|string|max:20|unique:books,isbn',
            'title'       => 'required|string|max:255',
            'author'      => 'nullable|string|max:255',
            'publisher'   => 'nullable|string|max:255',
            'year'        => 'nullable|integer|digits:4|min:1000|max:' . date('Y'),
            'pages'       => 'nullable|integer',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'file'        => 'nullable|file|mimes:pdf,epub|max:20480',
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('books', 'public');
        }

        Book::create($validated);

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan');
    }

    // Menampilkan form edit buku
    public function edit(Book $book)
    {
        $categories = Category::all();

        return Inertia::render('Books/Edit', [
            'book'       => $book,
            'categories' => $categories,
        ]);
    }

    // Update data buku
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'isbn'        => 'required|string|max:20|unique:books,isbn,' . $book->id,
            'title'       => 'required|string|max:255',
            'author'      => 'nullable|string|max:255',
            'publisher'   => 'nullable|string|max:255',
            'year'        => 'nullable|integer|digits:4|min:1000|max:' . date('Y'),
            'pages'       => 'nullable|integer',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'file'        => 'nullable|file|mimes:pdf,epub|max:20480',
        ]);

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
                Storage::disk('public')->delete($book->file_path);
            }

            $validated['file_path'] = $request->file('file')->store('books', 'public');
        }

        $book->update($validated);

        return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui');
    }

    // Hapus buku
    public function destroy(Book $book)
    {
        // Hapus file terkait kalau ada
        if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
            Storage::disk('public')->delete($book->file_path);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus');
    }
}
