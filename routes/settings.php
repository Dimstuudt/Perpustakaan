<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\PasswordController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::redirect('settings', '/settings/profile');

    // Profil
    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('settings/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('settings/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Password
    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::post('settings/password', [PasswordController::class, 'update'])->name('setting.password.update');

    // Avatar
    Route::post('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    // Tampilan / Appearance
    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');
});
