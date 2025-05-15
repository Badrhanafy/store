<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

// app/Http/Controllers/Auth/SocialAuthController.php

public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();
        
        $user = User::firstOrCreate(
            ['email' => $googleUser->email],
            [
                'name' => $googleUser->name,
                'password' => Hash::make(Str::random(24))
            ]
        );

        // For API responses
        if (request()->wantsJson()) {
            return response()->json([
                'user' => $user,
                'token' => $user->createToken('google-auth')->plainTextToken
            ]);
        }

        // For web flow
        auth()->login($user);
        return redirect('/dashboard');

    } catch (\Exception $e) {
        \Log::error('Google auth failed: '.$e->getMessage());
        
        if (request()->wantsJson()) {
            return response()->json(['error' => 'Authentication failed'], 401);
        }
        
        return redirect('/login')->withErrors([
            'google' => 'Google authentication failed'
        ]);
    }
}
public function handleGoogleToken(Request $request)
{
    try {
        $request->validate(['token' => 'required']);
        
        $googleUser = Socialite::driver('google')
                       ->scopes(['openid', 'profile', 'email'])
                        ->stateless()
                         ->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->email],
            [
                'name' => $googleUser->name,
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now()
            ]
        );

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('google-auth')->plainTextToken
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Google authentication failed',
            'error' => $e->getMessage()
        ], 401);
    }
}
public function handleGoogleAuth(Request $request)
{
    try {
        $request->validate(['credential' => 'required|string']);
        
        // Verify the Google ID token
        $googleUser = Socialite::driver('google')
            ->stateless()
            ->userFromToken($request->credential);

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $googleUser->email],
            [
                'name' => $googleUser->name,
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now()
            ]
        );

        // Create API token
        $token = $user->createToken('google-auth')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);

    } catch (\Exception $e) {
        \Log::error('Google auth failed: '.$e->getMessage());
        return response()->json([
            'message' => 'Authentication failed',
            'error' => $e->getMessage()
        ], 401);
    }
}
}
