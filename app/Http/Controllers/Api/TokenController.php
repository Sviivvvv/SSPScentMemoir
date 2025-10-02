<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    // POST /api/auth/token
    public function issue(Request $req)
    {
        $req->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
            'abilities' => 'array'
        ]);

        if (!Auth::attempt($req->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $abilities = $req->input('abilities', ['catalog:read', 'cart:*', 'orders:*']);
        $token = $req->user()->createToken($req->device_name, $abilities);

        return response()->json([
            'token' => $token->plainTextToken,
            'expires_in_minutes' => config('sanctum.expiration')
        ]);
    }

    public function revokeCurrent(Request $req)
    {
        $req->user()->currentAccessToken()?->delete();
        return response()->json(['message' => 'Token revoked']);
    }

    public function revokeAll(Request $req)
    {
        $req->user()->tokens()->delete();
        return response()->json(['message' => 'All tokens revoked']);
    }
}
