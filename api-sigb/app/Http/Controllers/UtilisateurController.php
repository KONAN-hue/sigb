<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UtilisateurController extends Controller
{
    // Ajout du middleware dans le constructeur
    public function __construct()
    {
        $this->middleware('auth:api'); // Vérifie si l'utilisateur est connecté
    }

    /**
     * Vérifie si l'utilisateur connecté est admin.
     */
    private function isAdmin()
    {
        $user = Auth::user();
        // Adaptez la condition selon votre modèle (ex: $user->is_admin ou $user->role === 'admin')
        return $user && $user->role === 'admin';
    }

    /**
     * Affiche la liste des utilisateurs.
     */
    public function index()
    {

        // Création d'exemples d'utilisateurs si la table est vide
        Utilisateur::create([
            'nom' => 'Admin Principal',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin1234'),
            'role' => 'admin'
        ]);
        Utilisateur::create([
            'nom' => 'Utilisateur Simple',
            'email' => 'user@example.com',
            'password' => bcrypt('user1234'),
            'role' => 'user'
        ]);

        if (!$this->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès refusé. Seuls les administrateurs peuvent effectuer cette action.',
                'data' => null
            ], 403);
        }

        $utilisateurs = Utilisateur::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Liste des utilisateurs récupérée avec succès.',
            'data' => $utilisateurs
        ], 200);
    }

    /**
     * Affiche le formulaire de création (non utilisé en API).
     */
    public function create()
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès refusé. Seuls les administrateurs peuvent effectuer cette action.',
                'data' => null
            ], 403);
        }

        return response()->json([
            'status' => 'info',
            'message' => 'Formulaire de création non disponible en API.',
            'data' => null
        ], 200);
    }

    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès refusé. Seuls les administrateurs peuvent effectuer cette action.',
                'data' => null
            ], 403);
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string'
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $utilisateur = Utilisateur::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Utilisateur créé avec succès.',
            'data' => $utilisateur
        ], 201);
    }

    /**
     * Affiche un utilisateur spécifique.
     */
    public function show(Utilisateur $utilisateur)
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès refusé. Seuls les administrateurs peuvent effectuer cette action.',
                'data' => null
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Utilisateur récupéré avec succès.',
            'data' => $utilisateur
        ], 200);
    }

    /**
     * Affiche le formulaire d'édition (non utilisé en API).
     */
    public function edit(Utilisateur $utilisateur)
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès refusé. Seuls les administrateurs peuvent effectuer cette action.',
                'data' => null
            ], 403);
        }

        return response()->json([
            'status' => 'info',
            'message' => 'Formulaire d\'édition non disponible en API.',
            'data' => null
        ], 200);
    }

    /**
     * Met à jour un utilisateur existant.
     */
    public function update(Request $request, Utilisateur $utilisateur)
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès refusé. Seuls les administrateurs peuvent effectuer cette action.',
                'data' => null
            ], 403);
        }

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:utilisateurs,email,' . $utilisateur->id,
            // Ajoutez d'autres champs selon votre modèle
        ]);

        $utilisateur->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Utilisateur mis à jour avec succès.',
            'data' => $utilisateur
        ], 200);
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy(Utilisateur $utilisateur)
    {
        if (!$this->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès refusé. Seuls les administrateurs peuvent effectuer cette action.',
                'data' => null
            ], 403);
        }

        $utilisateur->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Utilisateur supprimé avec succès.',
            'data' => null
        ], 200);
    }
}
