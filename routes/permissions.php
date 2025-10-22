<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;

Route::middleware(['auth', 'web', 'verified'])->group(function () {

    // 🔹 Index & Show
    Route::get('/permissions', [PermissionController::class, 'index'])
        ->name('permissions.index')
        ->middleware('permission:permissions.view');

    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])
        ->name('permissions.show')
        ->middleware('permission:permissions.view');

    // 🔹 Create & Store
   

    Route::post('/permissions', [PermissionController::class, 'store'])
        ->name('permissions.store')
        ->middleware('permission:permissions.create');

    // 🔹 Edit & Update (pakai POST karena hosting kadang blokir PUT)
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
        ->name('permissions.edit')
        ->middleware('permission:permissions.edit');

    Route::post('/permissions/{permission}/update', [PermissionController::class, 'update'])
        ->name('permissions.update')
        ->middleware('permission:permissions.edit');

    // 🔹 Delete (pakai POST biar aman)
    Route::post('/permissions/{permission}/delete', [PermissionController::class, 'destroy'])
        ->name('permissions.destroy')
        ->middleware('permission:permissions.delete');
});
