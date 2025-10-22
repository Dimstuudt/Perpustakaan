<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RackController;

Route::middleware(['auth', 'web', 'verified'])->group(function () {

    // ======================
    // Rack Routes
    // ======================

    // List semua rak
    Route::get('/racks', [RackController::class, 'index'])
        ->name('racks.index');

    // Form tambah rak
    Route::get('/racks/create', [RackController::class, 'create'])
        ->name('racks.create');

    // Simpan rak baru
    Route::post('/racks/store', [RackController::class, 'store'])
        ->name('racks.store');

    // Form edit rak
    Route::get('/racks/{rack}/edit', [RackController::class, 'edit'])
        ->name('racks.edit');

    // Update rak (ganti dari PUT → POST)
    Route::post('/racks/{rack}/update', [RackController::class, 'update'])
        ->name('racks.update');

    // Hapus rak (ganti dari DELETE → POST)
    Route::post('/racks/{rack}/delete', [RackController::class, 'destroy'])
        ->name('racks.destroy');
});
