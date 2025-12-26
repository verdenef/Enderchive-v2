<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'App\Http\Controllers\API'], function () {
    // --------------- Authentication Routes ----------------//
    Route::post('register', 'AuthenticationController@register')->name('register');
    Route::post('login', 'AuthenticationController@login')->name('login');
    
    // ------------------ Public Routes ----------------------//
    Route::get('games', 'GameController@index');
    Route::get('games/{game}', 'GameController@show');
    // Milestones listing (public)
    Route::get('games/{game}/milestones', 'MilestoneController@index');
    Route::get('platforms', 'PlatformController@index');
    Route::get('stores', 'StoreController@index');
    Route::get('genres', 'GenreController@index');
    Route::get('tags', 'TagController@index');
    Route::get('games/{game}/reviews', 'ReviewController@index');
    Route::get('games/{game}/editions', 'EditionController@index');
    
    // ------------------ Protected Routes ----------------------//
    // Admin routes use session-based authentication (for Inertia.js)
    // Using custom middleware that returns JSON 401 instead of redirecting
    // Cookie middleware is needed to decrypt session cookies
    Route::middleware([
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
        'auth.session.api'
    ])->group(function () {
        // Admin routes
        Route::get('admin/summary', 'AdminController@summary')->name('admin.summary');
        Route::get('admin/users', 'AdminController@users')->name('admin.users');
        Route::get('admin/activity', 'AdminController@activity')->name('admin.activity');
    });
    
    Route::middleware('auth:sanctum')->group(function () {
        // User management
        Route::get('get-user', 'AuthenticationController@userInfo')->name('get-user');
        Route::post('logout', 'AuthenticationController@logOut')->name('logout');
        Route::put('user/profile', 'UserController@updateProfile');
        Route::put('user/password', 'UserController@changePassword');
        Route::put('user/username', 'UserController@updateUsername');
        
        // User suggestions
        Route::get('users/suggested', 'UserController@suggested');
        Route::get('users/{userId}/profile', 'UserController@profile');
        
        // Game management
        Route::post('games', 'GameController@store');
        Route::put('games/{game}', 'GameController@update');
        Route::delete('games/{game}', 'GameController@destroy');
        
        // User games (library) 
        Route::get('user/games', 'UserGameController@index');
        Route::post('user/games', 'UserGameController@store');
        Route::get('user/games/{userGame}', 'UserGameController@show');
        Route::put('user/games/{userGame}', 'UserGameController@update');
        Route::delete('user/games/{userGame}', 'UserGameController@destroy');
        Route::post('user/games/{game}/status', 'UserGameController@setStatus');

        // Ownerships
        Route::get('user/games/{game}/ownerships', 'UserGameOwnershipController@index');
        Route::post('user/games/{game}/ownerships', 'UserGameOwnershipController@store');
        Route::delete('user/games/{game}/ownerships/{ownership}', 'UserGameOwnershipController@destroy');

        // Milestones (admin could protect with policy; leave as-is for now)
        Route::post('games/{game}/milestones', 'MilestoneController@store');

        // Progress marking
        Route::post('user/games/{game}/progress/{milestone}', 'UserGameProgressController@store');
        Route::delete('user/games/{game}/progress/{milestone}', 'UserGameProgressController@destroy');
        
        // Reviews
        Route::post('games/{game}/reviews', 'ReviewController@store');
        Route::put('reviews/{review}', 'ReviewController@update');
        Route::delete('reviews/{review}', 'ReviewController@destroy');
        
        // Friends
        Route::get('friends', 'FriendController@index');
        Route::get('friends/requests', 'FriendController@requests');
        Route::get('friends/search', 'FriendController@search');
        Route::post('friends', 'FriendController@store');
        Route::get('friends/{friend}', 'FriendController@show');
        Route::put('friends/{friend}', 'FriendController@update');
        Route::delete('friends/{friend}', 'FriendController@destroy');
        
        // Platform management (admin only)
        Route::post('platforms', 'PlatformController@store');
        Route::put('platforms/{platform}', 'PlatformController@update');
        Route::delete('platforms/{platform}', 'PlatformController@destroy');
        
        // Genre management (admin only)
        Route::post('genres', 'GenreController@store');
        Route::put('genres/{genre}', 'GenreController@update');
        Route::delete('genres/{genre}', 'GenreController@destroy');
        
        // Tag management (admin only)
        Route::post('tags', 'TagController@store');
        Route::put('tags/{tag}', 'TagController@update');
        Route::delete('tags/{tag}', 'TagController@destroy');
    });
});
