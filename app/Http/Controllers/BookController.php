<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BookController extends Controller
{
    // Menampilkan daftar buku aktif (non-deleted)
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
                'cover_path'  => $book->cover_path ? asset('storage/' . $book->cover_path) : null,
                'file_path'   => $book->file_path ? asset('storage/' . $book->file_path) : null,
            ]),
        ]);
    }

    // Menampilkan daftar buku yang dihapus (trashed)
    public function trashed()
    {
        $books = Book::onlyTrashed()->with('category')->latest()->paginate(10);

        return Inertia::render('Books/Trashed', [
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
                'cover_path'  => $book->cover_path ? asset('storage/' . $book->cover_path) : null,
                'file_path'   => $book->file_path ? asset('storage/' . $book->file_path) : null,
                'deleted_at'  => $book->deleted_at,
            ]),
        ]);
    }

    // Menampilkan form tambah buku
    public function create()
    {
        $categories = Category::all();

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
            'cover'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('books', 'public');
        }

        if ($request->hasFile('cover')) {
            $validated['cover_path'] = $request->file('cover')->store('covers', 'public');
        }

        Book::create($validated);

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan');
    }

    // Menampilkan form edit buku
    public function edit(Book $book)
    {
        $categories = Category::all();

        $book->cover_url = $book->cover_path ? asset('storage/' . $book->cover_path) : null;
        $book->file_url  = $book->file_path ? asset('storage/' . $book->file_path) : null;

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
            'cover'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Hapus file lama jika upload baru
        if ($request->hasFile('file') && $book->file_path && Storage::disk('public')->exists($book->file_path)) {
            Storage::disk('public')->delete($book->file_path);
            $validated['file_path'] = $request->file('file')->store('books', 'public');
        }

        // Hapus cover lama jika upload baru
        if ($request->hasFile('cover') && $book->cover_path && Storage::disk('public')->exists($book->cover_path)) {
            Storage::disk('public')->delete($book->cover_path);
            $validated['cover_path'] = $request->file('cover')->store('covers', 'public');
        }

        $book->update($validated);

        return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui');
    }

    // Soft delete buku
    public function destroy(Book $book)
    {
        $book->delete(); // ✅ soft delete
        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus');
    }

    // Restore buku dari trash
    public function restore($id)
    {
        $book = Book::withTrashed()->findOrFail($id);
        $book->restore();

        return redirect()->route('books.trashed')->with('success', 'Buku berhasil dikembalikan');
    }

    // Force delete buku permanen
    public function forceDelete($id)
    {
        $book = Book::withTrashed()->findOrFail($id);

        // Hapus file jika ada
        if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
            Storage::disk('public')->delete($book->file_path);
        }

        if ($book->cover_path && Storage::disk('public')->exists($book->cover_path)) {
            Storage::disk('public')->delete($book->cover_path);
        }

        $book->forceDelete(); // hapus permanen

        return redirect()->route('books.trashed')->with('success', 'Buku berhasil dihapus permanen');
    }

    // Preview buku
     // Preview buku
   public function preview(Book $book)
{
    $categories = Category::all();

    return Inertia::render('Books/Preview', [
        'book' => [
            'id'         => $book->id,
            'isbn'       => $book->isbn,
            'title'      => $book->title,
            'author'     => $book->author,
            'publisher'  => $book->publisher,
            'year'       => $book->year,
            'pages'      => $book->pages,
            'description'=> $book->description,
            'category_id'=> $book->category_id,
            'cover_url'  => $book->cover_path, // pastikan ini nama field di DB
            'file_url'   => $book->file_path,  // pastikan ini nama field di DB
        ],
        'categories' => $categories,
    ]);



}}
