<?php

use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
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
Route::get('/auth/google/redirect', function () {
    return Socialite::driver('google')->redirect();
});

Route::get('/auth/google/callback', function (Request $request) {
    $googleUser = Socialite::driver('google')->user();

    $user = \App\Models\User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        ['name' => $googleUser->getName()]
    );

    $token = $user->createToken('google-token')->plainTextToken;

    // redirect back to frontend with token
    return redirect("http://localhost:3000/google/callback?token=$token");
});



////////// Password reset 

Route::get('/oauth/google', [GoogleOAuthController::class, 'redirectToGoogle']);
Route::get('/oauth/google/callback', [GoogleOAuthController::class, 'handleGoogleCallback']);