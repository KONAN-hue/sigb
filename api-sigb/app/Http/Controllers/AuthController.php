<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Authentifie l'utilisateur et retourne un token JWT.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'motDePasse' => 'required|string'
        ]);

        // Renommer la clé pour correspondre à 'password' attendu par Auth::attempt
        $credentials['password'] = $credentials['motDePasse'];
        unset($credentials['motDePasse']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Identifiants invalides.',
                'data' => null
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Connexion réussie.',
            'data' => [
                'user' => Auth::user(),
                'token' => $token
            ]
        ], 200);
    }

    /**
     * Déconnecte l'utilisateur (invalide le token).
     */
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Déconnexion réussie.',
            'data' => null
        ], 200);
    }
}
