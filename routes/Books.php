<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookImportController;

// =================================
// Books Routes (GET & POST Only)
// =================================
Route::middleware(['auth', 'web', 'verified'])->group(function () {

    // Daftar buku (index)
    Route::get('/books', [BookController::class, 'index'])
        ->name('books.index')
        ->middleware('permission:books.view');

    // Form tambah buku
    Route::get('/books/create', [BookController::class, 'create'])
        ->name('books.create')
        ->middleware('permission:books.create');

    // Simpan buku baru
    Route::post('/books', [BookController::class, 'store'])
        ->name('books.store')
        ->middleware('permission:books.create');

    // Form edit buku
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])
        ->name('books.edit')
        ->middleware('permission:books.edit');

    // Update buku (gunakan POST bukan PUT)
    Route::post('/books/{book}/update', [BookController::class, 'update'])
        ->name('books.update')
        ->middleware('permission:books.edit');

    // Hapus buku (pakai POST, bukan DELETE)
    Route::post('/books/{id}/delete', [BookController::class, 'destroy'])
    ->name('books.destroy')
    ->middleware('permission:books.delete');


    // Buku yang dihapus (trash)
    Route::get('/books/trashed', [BookController::class, 'trashed'])
        ->name('books.trashed')
        ->middleware('permission:books.view');

    // Restore buku
    Route::post('/books/{id}/restore', [BookController::class, 'restore'])
        ->name('books.restore')
        ->middleware('permission:books.restore');

    // Hapus permanen buku (force delete)
    Route::post('/books/{id}/force-delete', [BookController::class, 'forceDelete'])
        ->name('books.forceDelete')
        ->middleware('permission:books.forceDelete');

    // Bulk Actions
    Route::post('/books/bulk-delete', [BookController::class, 'bulkDelete'])
        ->name('books.bulkDelete')
        ->middleware('permission:books.delete');

    Route::post('/books/bulk-restore', [BookController::class, 'bulkRestore'])
        ->name('books.bulkRestore')
        ->middleware('permission:books.restore');

    Route::post('/books/bulk-force-delete', [BookController::class, 'bulkForceDelete'])
        ->name('books.bulkForceDelete')
        ->middleware('permission:books.forceDelete');

    // Preview buku
    Route::get('/books/{book}/preview', [BookController::class, 'preview'])
        ->name('books.preview')
        ->middleware('permission:books.view');

    // Tambah banyak sekaligus
    Route::post('/books/multiple', [BookController::class, 'storeMultiple'])
        ->name('books.storeMultiple')
        ->middleware('permission:books.create');
});
