<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Redirect root to home
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);

// Authentication routes
Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])
    ->middleware('guest');

Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::get('/signup', function () {
    return Inertia::render('SignUp');
})->name('signup');

// Main application routes
Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])
    ->name('home');

Route::get('/library', [\App\Http\Controllers\LibraryController::class, 'index'])
    ->name('library')
    ->middleware('auth');

Route::get('/wishlist', [\App\Http\Controllers\WishlistController::class, 'index'])
    ->name('wishlist')
    ->middleware('auth');

Route::get('/friends', function () {
    return Inertia::render('Friends');
})->name('friends')->middleware('auth');

Route::get('/settings', function () {
    return Inertia::render('Settings');
})->name('settings')->middleware('auth');

Route::get('/profile', function () {
    return Inertia::render('Profile');
})->name('profile')->middleware('auth');

Route::get('/games/{id}', [\App\Http\Controllers\GameController::class, 'show'])
    ->name('games.show')
    ->middleware('auth');

Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])
    ->name('search');

Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])
    ->name('admin')
    ->middleware('auth');
