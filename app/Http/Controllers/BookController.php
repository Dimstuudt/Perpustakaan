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
                'type'        => $book->type,
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

public function storeMultiple(Request $request)
{
    $validated = $request->validate([
        'books' => 'required|array|max:5',
        'books.*.isbn'        => 'required|string|max:20|unique:books,isbn',
        'books.*.title'       => 'required|string|max:255',
        'books.*.author'      => 'nullable|string|max:255',
        'books.*.publisher'   => 'nullable|string|max:255',
        'books.*.year'        => 'nullable|integer|digits:4|min:1000|max:' . date('Y'),
        'books.*.pages'       => 'nullable|integer',
        'books.*.description' => 'nullable|string',
        'books.*.category_id' => 'nullable|exists:categories,id',
    ]);

    $savedBooks = [];
    $booksFiles = $request->file('books'); // ambil file nested array

    foreach ($request->books as $i => $bookData) {
        // Cover
        $coverFile = $booksFiles[$i]['cover'] ?? null;
        if ($coverFile instanceof \Illuminate\Http\UploadedFile) {
            $bookData['cover_path'] = $coverFile->store('covers', 'public');
        } else {
            $bookData['cover_path'] = 'images/dummy-cover.png';
        }

        // File buku
        $bookFile = $booksFiles[$i]['file'] ?? null;
        if ($bookFile instanceof \Illuminate\Http\UploadedFile) {
            $bookData['file_path'] = $bookFile->store('books', 'public');
        }

        // Hapus key supaya bisa masuk $fillable
        unset($bookData['cover'], $bookData['file']);

        $savedBooks[] = Book::create($bookData);
    }

    return redirect()->route('books.index')
        ->with('success', count($savedBooks) . ' buku berhasil disimpan!');
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
            'type'        => 'required|in:ebook,physical', // <-- baru
            'stock'       => 'required|integer|min:1',
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
        'type'        => 'required|in:ebook,physical',
        'description' => 'nullable|string',
        'file'        => 'nullable|file|mimes:pdf,epub|max:20480',
        'cover'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'type'        => 'required|in:ebook,physical',
        'stock'       => '|integer|min:1',
    ]);

    // Simpan file buku
    if ($request->hasFile('file')) {
        if ($book->file_path && Storage::disk('public')->exists($book->file_path)) {
            Storage::disk('public')->delete($book->file_path);
        }
        $validated['file_path'] = $request->file('file')->store('books', 'public');
    }

    // Simpan cover buku
    if ($request->hasFile('cover')) {
        if ($book->cover_path && Storage::disk('public')->exists($book->cover_path)) {
            Storage::disk('public')->delete($book->cover_path);
        }
        $validated['cover_path'] = $request->file('cover')->store('covers', 'public');
    }

    // Hapus kunci 'file' dan 'cover' biar gak bentrok
    unset($validated['file'], $validated['cover']);

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

// Bulk soft delete buku
public function bulkDelete(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return back()->withErrors(['message' => 'Tidak ada buku yang dipilih.']);
    }

    // Soft delete semua buku terpilih
    Book::whereIn('id', $ids)->delete();

    return back()->with('success', 'Buku terpilih berhasil dipindahkan ke sampah.');
}

// Bulk restore buku
public function bulkRestore(Request $request)
{
    $ids = $request->input('ids', []);

    if (!empty($ids)) {
        Book::onlyTrashed()->whereIn('id', $ids)->restore();
    }

    return redirect()->route('books.index')
        ->with('success', 'Buku terpilih berhasil direstore.');
}

public function bulkForceDelete(Request $request)
{
    $ids = $request->input('ids', []);

    if (!empty($ids)) {
        Book::onlyTrashed()->whereIn('id', $ids)->forceDelete();
    }

    return redirect()->route('books.index')
        ->with('success', 'Buku terpilih berhasil dihapus permanen.');
}

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

