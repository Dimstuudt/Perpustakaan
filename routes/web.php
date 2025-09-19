<?php

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Requests\Auth\LoginRequest;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Contracts\Permission;
use App\Http\Controllers\PermissionController;
use App\Http\Middleware\EnsureProfileComplete;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Session\Middleware\AuthenticateSession;



Route::get('/', function () {
    return redirect('login');

})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard')->with('message', 'Selamat datang');
})->middleware(['auth', 'verified'])->name('dashboard'); /*, 'verified' <- bila ingin wajib verifikasi email */
Auth::routes(['verify' => true]); // untuk verifikasi email
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.redirect');
Route::get('/clear-message', function () {
    session()->forget('message');
    return redirect()->back();
});
Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();
    $user = User::firstOrCreate(
        [
            'email' => $googleUser->getEmail(),
        ]

    );
    $user->assignRole('user');

    Auth::login($user);
    return redirect('/dashboard')->with("message", "Selamat datang");
});
Route::post('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

// Roles and Permissions mulai disini
// users
Route::middleware(['auth', 'web', 'verified'])->group(function () {


Route::resource("users", UserController::class)
->only(['create','store'])
->middleware("permission:users.create");

Route::resource("users", UserController::class)
->only(['edit','update'])
->middleware("permission:users.edit");

Route::resource("users", UserController::class)
->only(['destroy'])
->middleware("permission:users.delete");

Route::put('/users/{user}/toggleStatus', [UserController::class, 'toggleStatus'])
    ->name('users.toggleStatus')
    ->middleware('permission:users.toggleStatus');


Route::resource("users", UserController::class)
->only(['index', 'show'])
->middleware("permission:users.create|users.edit|users.delete|users.view");

Route::resource("users", UserController::class)
->only(['show'])
->middleware("permission:users.show");

// roles
Route::resource("roles", RoleController::class)
->only(['create','store'])
->middleware("permission:roles.create");

Route::resource("roles", RoleController::class)
->only(['edit','update'])
->middleware("permission:roles.edit");

Route::resource("roles", RoleController::class)
->only(['destroy'])
->middleware("permission:roles.delete");

Route::resource("roles", RoleController::class)
->only(['index','show'])
->middleware("permission:roles.create|roles.edit|roles.delete|roles.view");

// permissions
Route::resource("permissions", PermissionController::class)
->only(['create','store'])
->middleware("permission:permissions.create");

Route::resource("permissions", PermissionController::class)
->only(['edit','update'])
->middleware("permission:permissions.edit");

Route::resource("permissions", PermissionController::class)
->only(['destroy'])
->middleware("permission:permissions.delete");

Route::resource("permissions", PermissionController::class)
->only(['index','show'])
->middleware("permission:permissions.create|permissions.edit|permissions.delete|permissions.view");

// Logs
Route::resource("logs", LogController::class)
->only(['index','show'])
->middleware("permission:logs.view");


// books
Route::resource("books", App\Http\Controllers\BookController::class)
    ->middleware("permission:books.create|books.edit|books.delete|books.view");

Route::resource("books", App\Http\Controllers\BookController::class)
    ->only(['create','store'])
    ->middleware("permission:books.create");

Route::resource("books", App\Http\Controllers\BookController::class)
    ->only(['edit','update'])
    ->middleware("permission:books.edit");

Route::resource("books", App\Http\Controllers\BookController::class)
    ->only(['destroy'])
    ->middleware("permission:books.delete");

Route::resource("books", App\Http\Controllers\BookController::class)
    ->only(['index','show'])
    ->middleware("permission:books.view");

Route::get('/books/{book}/preview', [App\Http\Controllers\BookController::class, 'preview'])
    ->name('books.preview')
    ->middleware('permission:books.view');



// Categories (tanpa show)
Route::resource('categories', CategoryController::class)->except(['show'])
    ->middleware([
        'index'   => 'permission:categories.view',
        'create'  => 'permission:categories.create',
        'store'   => 'permission:categories.create',
        'edit'    => 'permission:categories.edit',
        'update'  => 'permission:categories.edit',
        'destroy' => 'permission:categories.delete',
    ]);

// Soft delete tambahan
Route::get('categories/trashed', [CategoryController::class, 'trashed'])
    ->name('categories.trashed')
    ->middleware('permission:categories.view');

Route::put('categories/{id}/restore', [CategoryController::class, 'restore'])
    ->name('categories.restore')
    ->middleware('permission:categories.restore');


Route::delete('categories/{id}/force-delete', [CategoryController::class, 'forceDelete'])
    ->name('categories.forceDelete')
    ->middleware('permission:categories.forceDelete');

    // Bulk delete
   Route::post('categories/bulk-delete', [CategoryController::class, 'bulkDelete'])
    ->name('categories.bulkDelete')
    ->middleware('permission:categories.delete');

    // Bulk restore
    Route::post('categories/bulk-restore', [CategoryController::class, 'bulkRestore'])
    ->name('categories.bulkRestore')
    ->middleware('permission:categories.restore');

    // Bulk force delete
    Route::post('categories/bulk-force-delete', [CategoryController::class, 'bulkForceDelete'])
    ->name('categories.bulkForceDelete')
    ->middleware('permission:categories.forceDelete');

});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
