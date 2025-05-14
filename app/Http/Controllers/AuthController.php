<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // cherche user b email
        $user = User::where('email', $request->email)->first();

        // check si user ma kaynach wla password ghalat
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // check role admin
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        // generate token
        $token = $user->createToken('admin-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
}

