<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Validator;

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

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email',
            'motDePasse' => 'required|string|min:6'
        ], [
            'nom.required' => 'Le nom est obligatoire',
            'prenoms.required' => 'Le prénom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être une adresse valide',
            'email.unique' => 'Cet email est déjà utilisé',
            'motDePasse.required' => 'Le mot de passe est obligatoire',
            'motDePasse.min' => 'Le mot de passe doit faire au moins 6 caractères'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation échouée',
                'errors' => $validator->errors(),
                'data' => null
            ], 422);
        }

        $data = $validator->validated();
        
        $user = Utilisateur::create([
            'nom' => $data['nom'],
            'prenoms' => $data['prenoms'],
            'email' => $data['email'],
            'password' => bcrypt($data['motDePasse']), // Notez que dans la BD on garde 'password'
            'role' => 'user'
        ]);

        $token = auth()->login($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Compte créé avec succès',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }
}
