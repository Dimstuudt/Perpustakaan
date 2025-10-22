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

    // Permissions create
    Route::get('/permissions/create', [PermissionController::class, 'create'])
        ->name('permissions.create')
        ->middleware('permission:permissions.create');

    // -------------------------------
    // Logs
    // -------------------------------
    Route::resource('logs', LogController::class)->only(['index', 'show'])
        ->middleware('permission:logs.view');
});

// =================================
// Loan Routes (GET & POST only)
// =================================

// User pinjam buku
Route::post('loans', [LoanController::class, 'store'])
    ->name('loans.store')
    ->middleware(['auth', 'role:user']);

// User cek status pinjaman
Route::get('/user/loans/status', [UserLoanController::class, 'status'])
    ->name('user.loans.status');

// Admin lihat daftar peminjaman
Route::get('loans', [LoanController::class, 'index'])
    ->name('loans.index')
    ->middleware(['auth', 'permission:loans.view']);

// Admin approve, reject, return (POST)
Route::post('loans/{loan}/approve', [LoanController::class, 'approve'])
    ->name('loans.approve')
    ->middleware(['auth', 'permission:loans.approve']);

Route::post('loans/{loan}/reject', [LoanController::class, 'reject'])
    ->name('loans.reject')
    ->middleware(['auth', 'permission:loans.reject']);

Route::post('loans/{loan}/return', [LoanController::class, 'return'])
    ->name('loans.return')
    ->middleware(['auth', 'permission:loans.return']);

// =================================
// User Loan Routes
// =================================
Route::middleware(['auth'])->prefix('user')->group(function () {
    Route::get('/loansuser', [UserLoanController::class, 'index'])->name('user.loans.index');
    Route::post('/loansuser', [UserLoanController::class, 'store'])->name('user.loans.store');
    Route::get('/loansuser/{id}', [UserLoanController::class, 'show'])->name('user.loans.show');
});

// Cancel pinjaman (POST)
Route::post('/user/loans/{id}/cancel', [UserLoanController::class, 'cancel'])
    ->name('loans.cancel');

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

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

// =================================
// Extra
// =================================
require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/users.php';
require __DIR__ . '/roles.php';
require __DIR__ . '/racks.php';
require __DIR__ . '/cabinets.php';
require __DIR__ . '/permissions.php';
require __DIR__ . '/Books.php';
require __DIR__ . '/categories.php';
