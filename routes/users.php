<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware(['auth', 'web', 'verified'])->group(function () {
    // Create & Store
    Route::resource('users', UserController::class)->only(['create', 'store'])
        ->middleware('permission:users.create');

    // Edit & Update
    Route::resource('users', UserController::class)->only(['edit', 'update'])
        ->middleware('permission:users.edit');

    // Delete
    Route::resource('users', UserController::class)->only(['destroy'])
        ->middleware('permission:users.delete');

    // Toggle Status
    Route::put('/users/{user}/toggleStatus', [UserController::class, 'toggleStatus'])
        ->name('users.toggleStatus')
        ->middleware('permission:users.toggleStatus');

    // Index & Show
    Route::resource('users', UserController::class)->only(['index', 'show'])
        ->middleware('permission:users.view');
});
