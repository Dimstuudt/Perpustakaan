<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\{
    LogController,
    UserLoanController,
    CategoryController,
    LandingController,
    BookController,
    PermissionController,
    PublicController,
    Settings\ProfileController,
    LoanController,
    BookImportController
};

// =================================
// Default Routes & Auth
// =================================
Route::get('/', fn () => redirect('welcome'))->name('home');

Route::get('/welcome', [LandingController::class, 'welcome'])->name('landing.welcome');
Route::get('/book/{id}', [LandingController::class, 'preview'])->name('public.preview');

Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Google Login
Route::get('/auth/google', fn () => Socialite::driver('google')->redirect())
    ->name('google.redirect');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::firstOrCreate(['email' => $googleUser->getEmail()]);
    $user->assignRole('user'); // default role

    Auth::login($user);
    return redirect('/dashboard')->with("message", "Selamat datang");
});

Route::get('/clear-message', fn () => redirect()->back()->without(['message']));
Route::post('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

// =================================
// Protected Routes (Auth & Verified)
// =================================
Route::middleware(['auth', 'web', 'verified'])->group(function () {

    // -------------------------------
    // Permissions
    // -------------------------------
    Route::resource('permissions', PermissionController::class)->only(['create', 'store'])
        ->middleware('permission:permissions.create');

    Route::resource('permissions', PermissionController::class)->only(['edit', 'update'])
        ->middleware('permission:permissions.edit');

    Route::resource('permissions', PermissionController::class)->only(['destroy'])
        ->middleware('permission:permissions.delete');

    Route::resource('permissions', PermissionController::class)->only(['index', 'show'])
        ->middleware('permission:permissions.view');

    // -------------------------------
    // Logs
    // -------------------------------
    Route::resource('logs', LogController::class)->only(['index', 'show'])
        ->middleware('permission:logs.view');

    // -------------------------------
    // Books
    // -------------------------------
    Route::get('/books/trashed', [BookController::class, 'trashed'])
        ->name('books.trashed')
        ->middleware('permission:books.view');

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

    // Resource tanpa show
    Route::resource('books', BookController::class)
        ->except(['show'])
        ->middleware('permission:books.view|books.create|books.edit|books.delete');

    Route::get('/books/{book}/preview', [BookController::class, 'preview'])
        ->name('books.preview')
        ->middleware('permission:books.view');

    // Restore & Force Delete
    Route::put('/books/{id}/restore', [BookController::class, 'restore'])
        ->name('books.restore')
        ->middleware('permission:books.restore');

    Route::delete('/books/{id}/force-delete', [BookController::class, 'forceDelete'])
        ->name('books.forceDelete')
        ->middleware('permission:books.forceDelete');

    // Multiple store
    Route::post('/books/multiple', [BookController::class, 'storeMultiple'])
        ->name('books.storeMultiple');

    // -------------------------------
    // Categories
    // -------------------------------
    Route::resource('categories', CategoryController::class)->except(['show'])->middleware([
        'index'   => 'permission:categories.view',
        'create'  => 'permission:categories.create',
        'store'   => 'permission:categories.create',
        'edit'    => 'permission:categories.edit',
        'update'  => 'permission:categories.edit',
        'destroy' => 'permission:categories.delete',
    ]);

    Route::get('categories/trashed', [CategoryController::class, 'trashed'])
        ->name('categories.trashed')
        ->middleware('permission:categories.view');

    Route::put('categories/{id}/restore', [CategoryController::class, 'restore'])
        ->name('categories.restore')
        ->middleware('permission:categories.restore');

    Route::delete('categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])
        ->name('categories.forceDelete')
        ->middleware('permission:categories.forceDelete');

    // Bulk Actions
    Route::post('categories/bulk-delete', [CategoryController::class, 'bulkDelete'])
        ->name('categories.bulkDelete')
        ->middleware('permission:categories.delete');

    Route::post('categories/bulk-restore', [CategoryController::class, 'bulkRestore'])
        ->name('categories.bulkRestore')
        ->middleware('permission:categories.restore');

    Route::post('categories/bulk-force-delete', [CategoryController::class, 'bulkForceDelete'])
        ->name('categories.bulkForceDelete')
        ->middleware('permission:categories.forceDelete');
});

// routes/web.php
Route::get('/categoried', [CategoryController::class, 'list'])->name('categories.list');
Route::get('/categories/{category}', [CategoryController::class, 'detail'])->name('categories.detail');


// =================================
// Loan Routes
// =================================

// User pinjam buku
Route::post('loans', [LoanController::class, 'store'])
    ->name('loans.store')
    ->middleware(['auth', 'role:user']);

// User cek status pinjaman
Route::get('/user/loans/status', [UserLoanController::class, 'status'])->name('user.loans.status');

// Admin lihat daftar peminjaman
Route::get('loans', [LoanController::class, 'index'])
    ->name('loans.index')
    ->middleware(['auth', 'permission:loans.view']);

// Admin approve, reject, return
Route::put('loans/{loan}/approve', [LoanController::class, 'approve'])
    ->name('loans.approve')
    ->middleware(['auth', 'permission:loans.approve']);

Route::put('loans/{loan}/reject', [LoanController::class, 'reject'])
    ->name('loans.reject')
    ->middleware(['auth', 'permission:loans.reject']);

Route::put('loans/{loan}/return', [LoanController::class, 'return'])
    ->name('loans.return')
    ->middleware(['auth', 'permission:loans.return']);

// User routes
Route::middleware(['auth'])->prefix('user')->group(function () {
    Route::get('/loansuser', [UserLoanController::class, 'index'])->name('user.loans.index');
    Route::post('/loansuser', [UserLoanController::class, 'store'])->name('user.loans.store');
    Route::get('/loansuser/{id}', [UserLoanController::class, 'show'])->name('user.loans.show');
});

// Cancel pinjaman
Route::delete('/user/loans/{id}/cancel', [UserLoanController::class, 'cancel'])->name('loans.cancel');

// =================================
// Public Collections
// =================================
Route::get('/koleksi', [PublicController::class, 'koleksi'])->name('koleksi');

// =================================
// Import Books
// =================================
Route::middleware(['auth'])->prefix('books')->group(function () {
    Route::get('/import', [BookImportController::class, 'index'])->name('books.import');
    Route::post('/import', [BookImportController::class, 'store'])->name('books.import.store');
});

// =================================
// Extra
// =================================
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/users.php';
require __DIR__ . '/roles.php';
require __DIR__ . '/racks.php';
require __DIR__ . '/cabinets.php';
