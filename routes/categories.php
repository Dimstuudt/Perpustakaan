<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

Route::middleware(['auth', 'web', 'verified'])->group(function () {

    // ======================
    // Category Routes
    // ======================

    // Daftar kategori
    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('categories.index')
        ->middleware('permission:categories.view');

    // Form tambah kategori
    Route::get('/categories/create', [CategoryController::class, 'create'])
        ->name('categories.create')
        ->middleware('permission:categories.create');

    // Simpan kategori baru
    Route::post('/categories/store', [CategoryController::class, 'store'])
        ->name('categories.store')
        ->middleware('permission:categories.create');

    // Form edit kategori
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
        ->name('categories.edit')
        ->middleware('permission:categories.edit');

    // Update kategori (POST ganti PUT)
    Route::post('/categories/{category}/update', [CategoryController::class, 'update'])
        ->name('categories.update')
        ->middleware('permission:categories.edit');

    // Hapus kategori (POST ganti DELETE)
    Route::post('/categories/{category}/delete', [CategoryController::class, 'destroy'])
        ->name('categories.destroy')
        ->middleware('permission:categories.delete');

    // ======================
    // Trashed & Restore
    // ======================
    Route::get('/categories/trashed', [CategoryController::class, 'trashed'])
        ->name('categories.trashed')
        ->middleware('permission:categories.view');

    Route::post('/categories/{id}/restore', [CategoryController::class, 'restore'])
        ->name('categories.restore')
        ->middleware('permission:categories.restore');

    Route::post('/categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])
        ->name('categories.forceDelete')
        ->middleware('permission:categories.forceDelete');

    // ======================
    // Bulk Actions
    // ======================
    Route::post('/categories/bulk-delete', [CategoryController::class, 'bulkDelete'])
        ->name('categories.bulkDelete')
        ->middleware('permission:categories.delete');

    Route::post('/categories/bulk-restore', [CategoryController::class, 'bulkRestore'])
        ->name('categories.bulkRestore')
        ->middleware('permission:categories.restore');

    Route::post('/categories/bulk-force-delete', [CategoryController::class, 'bulkForceDelete'])
        ->name('categories.bulkForceDelete')
        ->middleware('permission:categories.forceDelete');
});

// =================================
// Public category listing & detail
// =================================
Route::get('/categoried', [CategoryController::class, 'list'])
    ->name('categories.list');

Route::get('/categories/{category}', [CategoryController::class, 'detail'])
    ->name('categories.detail');
