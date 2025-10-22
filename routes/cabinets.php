<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CabinetController;

Route::middleware(['auth', 'web', 'verified'])->group(function () {

    // ======================
    // Cabinet Routes
    // ======================

    // List semua cabinet
    Route::get('/cabinets', [CabinetController::class, 'index'])
        ->name('cabinets.index');

    // Form tambah cabinet
    Route::get('/cabinets/create', [CabinetController::class, 'create'])
        ->name('cabinets.create');

    // Simpan cabinet baru
    Route::post('/cabinets/store', [CabinetController::class, 'store'])
        ->name('cabinets.store');

    // Form edit cabinet
    Route::get('/cabinets/{cabinet}/edit', [CabinetController::class, 'edit'])
        ->name('cabinets.edit');

    // Update cabinet (ganti dari PUT → POST)
    Route::post('/cabinets/{cabinet}/update', [CabinetController::class, 'update'])
        ->name('cabinets.update');

    // Hapus cabinet (ganti dari DELETE → POST)
    Route::post('/cabinets/{cabinet}/delete', [CabinetController::class, 'destroy'])
        ->name('cabinets.destroy');
});
