<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;

Route::middleware(['auth', 'web', 'verified'])->group(function () {

    /**
     * 🔹 CREATE & STORE
     * Harus di atas biar /roles/create gak ketiban /roles/{role}
     */
    Route::get('/roles/create', [RoleController::class, 'create'])
        ->name('roles.create')
        ->middleware('permission:roles.create');

    Route::post('/roles', [RoleController::class, 'store'])
        ->name('roles.store')
        ->middleware('permission:roles.create');

    /**
     * 🔹 EDIT & UPDATE
     */
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->name('roles.edit')
        ->middleware('permission:roles.edit');

    // PUT → POST (biar jalan di hosting)
    Route::post('/roles/{role}/update', [RoleController::class, 'update'])
        ->name('roles.update')
        ->middleware('permission:roles.edit');

    /**
     * 🔹 DELETE
     */
    Route::post('/roles/{role}/delete', [RoleController::class, 'destroy'])
        ->name('roles.destroy')
        ->middleware('permission:roles.delete');

    /**
     * 🔹 INDEX & SHOW
     * Paling bawah karena /roles/{role} bisa nabrak /roles/create kalo di atas
     */
    Route::get('/roles', [RoleController::class, 'index'])
        ->name('roles.index')
        ->middleware('permission:roles.view');

    Route::get('/roles/{role}', [RoleController::class, 'show'])
        ->name('roles.show')
        ->middleware('permission:roles.view');
});
