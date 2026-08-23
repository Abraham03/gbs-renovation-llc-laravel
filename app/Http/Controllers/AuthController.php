<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTGuard;

class AuthController extends Controller
{
    /**
     * Helper para que el editor de código reconozca los métodos de JWT.
     */
    protected function guard(): JWTGuard
    {
        return auth('api');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Usamos nuestro helper $this->guard() en lugar de auth('api')
        if (! $token = $this->guard()->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        return $this->respondWithToken((string) $token);
    }

    public function me()
    {
        return response()->json($this->guard()->user());
    }

    public function logout()
    {
        $this->guard()->logout();
        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }

    public function refresh()
    {
        return $this->respondWithToken((string) $this->guard()->refresh());
    }

    // Tipamos explícitamente $token como string
    protected function respondWithToken(string $token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60,
            'user' => $this->guard()->user()
        ]);
    }
}