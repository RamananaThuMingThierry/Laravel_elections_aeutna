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
             'message' => 'Vous êtes connecter',
             'status' => 200]);
    });

    /** --------------------------------- Récupérer user authentifier ------------------------------**/
    Route::get('getUser', [AuthController::class, 'getUserId']);

    /** ------------------------------------------- Statistiques ------------------------------------ **/
    Route::get('statistiques', [ElecteursController::class, 'statistiques']);

    /** ------------------------------------- Déconnection -------------------------------------------**/
    Route::post('logout', [AuthController::class, 'logout']);


    /** -------------------------------------------  Electeurs Membres -------------------------------- **/
    Route::get('export_liste_des_electeurs_membres', [ElecteursController::class, 'export_liste_des_electeurs_membres']);
    Route::get('liste_des_electeurs_membres', [ElecteursController::class, 'liste_des_electeurs_membres']);
    Route::get('recherche_un_electeur_membre/{propriete}/{value}', [ElecteursController::class, 'recherche_un_electeur_membre']);   
    Route::post('ajouter_un_electeur_membre', [ElecteursController::class, 'ajouter_un_electeur_membre']);   
    Route::get('afficher_un_electeur_membre/{id}', [ElecteursController::class, 'afficher_un_electeur_membre']);
    Route::get('obtenir_un_electeur/{id}', [ElecteursController::class, 'obtenir_un_electeur']);
    Route::post('modifier_un_electeur_membre/{id}', [ElecteursController::class, 'modifier_un_electeur_membre']);

    /** --------------------------------------------- Electeurs Non Adhére ------------------------------------- **/
    Route::get('liste_des_electeurs_non_adheres', [ElecteursController::class, 'liste_des_electeurs_non_adheres']);
    Route::post('ajouter_un_electeur_non_adhere', [ElecteursController::class, 'ajouter_un_electeur_non_adhere']);  
    Route::post('ajouter_un_electeur_non_adhere', [ElecteursController::class, 'ajouter_un_electeur_non_adhere']); 
    Route::get('obtenir_un_electeur_non_adhere/{id}', [ElecteursController::class, 'obtenir_un_electeur_non_adhere']);
    Route::get('afficher_un_electeur_non_adhere/{id}', [ElecteursController::class, 'afficher_un_electeur_non_adhere']);
    Route::post('modifier_un_electeur_non_adhere/{id}', [ElecteursController::class, 'modifier_un_electeur_non_adhere']); // Modifier un électeur non adhéré
    Route::get('recherche_electeur_non_adhere/{propriete}/{value}', [ElecteursController::class, 'recherche_electeur_non_adhere']);   
    
    /** ----------------------------------------------- Electeurs Votes ------------------------------------ **/
    Route::get('liste_des_electeurs_votes', [ElecteursController::class, 'liste_des_electeurs_votes']); 
    Route::get('recherche_un_electeur_vote/{propriete}/{value}', [ElecteursController::class, 'recherche_un_electeur_vote']);   
    Route::get('afficher_un_electeur_membre_vote/{id}', [ElecteursController::class, 'afficher_un_electeur_membre_vote']);
    Route::get('afficher_un_electeur_non_adhere_vote/{id}', [ElecteursController::class, 'afficher_un_electeur_non_adhere_vote']);
    Route::get('approuve_un_electeur_membre/{id}', [ElecteursController::class, 'approuve_un_electeur_membre']);
    Route::post('valide_un_electeur_membre/{id}', [ElecteursController::class, 'valide_un_electeur_membre']); 
    Route::post('desapprouve_un_electeur_vote/{id}', [ElecteursController::class, 'desapprouve_un_electeur_vote']);

    Route::post('delete-electeur/{id}', [ElecteursController::class, 'destroy']); // Supprimer une électeur
    
    /*------------------------------------------------------ Utilisateurs -----------------------------------**/

    Route::get('liste_des_utilisateurs', [UsersController::class, 'liste_des_utilisateurs']);
    Route::get('afficher_un_utilisateur/{id}', [UsersController::class, 'afficher_un_utilisateur']);
    Route::get('recherche_un_utilisateur/{propriete}/{value}', [UsersController::class, 'recherche_un_utilisateur']);   
    Route::get('obtenir_un_utilisateur/{id}', [UsersController::class, 'obtenir_un_utilisateur']); 
    Route::post('modifier_un_utilisateur/{id}', [UsersController::class, 'modifier_un_utilisateur']);
    Route::post('supprimer_un_utilisateur/{id}', [UsersController::class, 'supprimer_un_utilisateur']);

    /**------------------------------------------------ Profile --------------------------------------------*/
    Route::post('modifier_profile/{id}', [UsersController::class, 'modifier_un_utilisateur']);
});