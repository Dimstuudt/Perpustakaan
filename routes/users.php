<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware(['auth', 'web', 'verified'])->group(function () {

    /**
     * 🔹 CREATE & STORE
     * Harus di atas route {user}, biar /users/create gak ketiban
     */
    Route::get('/users/create', [UserController::class, 'create'])
        ->name('users.create')
        ->middleware('permission:users.create');

    Route::post('/users', [UserController::class, 'store'])
        ->name('users.store')
        ->middleware('permission:users.create');

    /**
     * 🔹 EDIT & UPDATE
     * edit harus di atas {user}, kalau gak /users/1/edit bisa dikira /users/{user}
     */
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit')
        ->middleware('permission:users.edit');

    Route::post('/users/{user}/update', [UserController::class, 'update']) // ganti PUT → POST
        ->name('users.update')
        ->middleware('permission:users.edit');

    /**
     * 🔹 TOGGLE STATUS
     */
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggleStatus')
        ->middleware('permission:users.toggleStatus');

    /**
     * 🔹 DELETE
     */
    Route::post('/users/{user}/delete', [UserController::class, 'destroy']) // DELETE → POST
        ->name('users.destroy')
        ->middleware('permission:users.delete');

    /**
     * 🔹 INDEX & SHOW
     * ini paling bawah karena {user} bisa nangkep apa pun setelah /users/
     */
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index')
        ->middleware('permission:users.view');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->name('users.show')
        ->middleware('permission:users.view');
});
