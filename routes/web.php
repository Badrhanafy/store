<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\GoogleOAuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/reset-password/{token}', function ($token) {
    $email = request('email');
    return redirect()->away("http://localhost:3000/reset-password?token=$token&email=$email");
})->name('password.reset');
////////////////// auuuuuuuth googlr
Route::get('/', function () {
    return view('welcome');
});

// Password reset (keep this as is)
Route::get('/reset-password/{token}', function ($token) {
    $email = request('email');
    return redirect()->away("http://localhost:3000/reset-password?token=$token&email=$email");
})->name('password.reset');

// Google OAuth using Socialite
Route::get('/auth/google/redirect', [\App\Http\Controllers\Auth\GoogleOAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleOAuthController::class, 'handleGoogleCallback']);



////////// Password reset 

Route::get('/oauth/google', [GoogleOAuthController::class, 'redirectToGoogle']);
Route::get('/oauth/google/callback', [GoogleOAuthController::class, 'handleGoogleCallback']);