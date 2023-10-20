<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\ElecteursController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function(){

    Route::get('/checkingAuthenticated', function(){
        return response()->json(
            [
             'message' => 'You are in',
             'status' => 200]);
    });

    // Afficher listes des électeurs membres AEUTNA
    Route::get('membres', [ElecteursController::class, 'membres']);
    
    // Afficher tous les électeurs
    Route::get('liste_des_electeurs', [ElecteursController::class, 'liste_des_electeurs']); 
    
    // Afficher tous les électeurs membres
    Route::get('liste_des_electeurs_membres', [ElecteursController::class, 'liste_des_electeurs_membres']); 
    
    // Afficher tous les électeurs non adhéré
    Route::get('liste_des_electeurs_non_adhere', [ElecteursController::class, 'liste_des_electeurs_non_adhere']); 

    // Créer un électeur membres AEUTNA
    Route::post('store-electeur', [ElecteursController::class, 'store']);   
    
    // Créer un électeur nouveau bachelier
    Route::post('nouveau-bachelier-electeur', [ElecteursController::class, 'nouveau_bachelier']);   // Créer un nouveau bachelier
    Route::get('show-electeur/{id}', [ElecteursController::class, 'show']); // Afficher un électeur
    Route::get('edit-electeur/{id}', [ElecteursController::class, 'edit']); // Modifier un électeur
    Route::put('update-electeur/{id}', [ElecteursController::class, 'update']); // Modifier un électeur
    Route::delete('delete-electeur/{id}', [ElecteursController::class, 'destroy']); // Supprimer une électeur
   
    // Déconnexion
    Route::post('logout', [AuthController::class, 'logout']);
    
    // users
    Route::get('users', [UsersController::class, 'index']);   // Affichier des utilisateurs
    // Route::get('see-users/{id}', [UsersController::class, 'show']);   // Voir un utilisateur
    // Route::put('update-users/{id}', [UsersController::class, 'update']);   // Modifier un utilisateur
    // Route::post('store-users', [UsersController::class, 'store']);   // Créer un utilisateur
});