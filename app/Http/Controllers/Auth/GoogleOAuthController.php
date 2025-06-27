<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
  use Laravel\Socialite\Facades\Socialite;
class GoogleOAuthController extends Controller
{
 

public function redirectToGoogle()
{
    return Socialite::driver('google')
        ->redirect();
}

public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->user();
        
        $user = User::firstOrCreate(
            ['email' => $googleUser->email],
            [
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
                'password' => bcrypt(Str::random(24))
            ]
        );

        $token = $user->createToken('google-token')->plainTextToken;

        return redirect("http://localhost:3000/google/callback?token=$token");

    } catch (\Exception $e) {
        Log::error('Google OAuth Error: ' . $e->getMessage());
        return redirect("http://localhost:3000/login?error=google_auth_failed");
    }
}
}