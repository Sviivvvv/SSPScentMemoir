<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;   
use App\Models\User;                   

class TokenController extends Controller
{
    /**
     * POST /api/auth/token
     * Body: { email, password, device_name, abilities?[] }
     * Returns: { token, expires_in_minutes }
     */
    public function issue(Request $req)
    {
        $validated = $req->validate([
            'email'       => 'required|email',
            'password'    => 'required',
            'device_name' => 'required',
            'abilities'   => 'array'
        ]);

       
        $user = User::where('email', $validated['email'])->first();
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $abilities = $req->input('abilities', ['catalog:read', 'cart:*', 'orders:*']);
        $token = $user->createToken($validated['device_name'], $abilities);

        return response()->json([
            'token' => $token->plainTextToken,
            'type'  => 'Bearer',
            'abilities' => $abilities,
            'expires_in_minutes' => config('sanctum.expiration'),
        ], 200);
    }

    /** DELETE /api/auth/token (current token) */
    public function revokeCurrent(Request $req)
    {
        $req->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Token revoked']);
    }

    /** DELETE /api/auth/tokens (all my tokens) */
    public function revokeAll(Request $req)
    {
        $req->user()->tokens()->delete();
        return response()->json(['message' => 'All tokens revoked']);
    }
}
