<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function liste_des_utilisateurs()
    {
        $users = User::all();
        return response()->json([
            'status' => 200,
            'liste_des_utilisateurs' => $users
        ]);
    }

    public function recherche_un_utilisateur(string $propriete, string $value){ 
        

           $verifier_un_utilisateur = DB::table('users')->where($propriete,'like',"%$value%")->exists();
     
           if($verifier_un_utilisateur){
                $recherche_un_utilisateur = User::where($propriete,'like',"%$value%")->get();
                return response()->json([
                    'status' => 200,
                    'utilisateurs' => $recherche_un_utilisateur
                ]);
           }else{
              return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat!'
              ]);
           }
    }

    /**
     * Display the specified resource.
     */
    public function obtenir_un_utilisateur(string $id)
    {
        try {
            $user = DB::table('users')->where('id', $id)->first();
                    if ($user) {
            return response()->json(['user' => $user, 'status' => 200], 200);
                    } else {
                        return response()->json(['message' => 'Utilisateur non trouvé', 'status' => 404], 404);
                    }
        } catch (\Exception $e) {
    // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
            return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500], 500);
        }
    }
    
    public function afficher_un_utilisateur(string $id)
    {
        try {
            $user = DB::table('users')->where('id', $id)->first();
                    if ($user) {
            return response()->json(['user' => $user, 'status' => 200], 200);
                    } else {
                        return response()->json(['message' => 'Utilisateur non trouvé', 'status' => 404], 404);
                    }
        } catch (\Exception $e) {
    // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
            return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500], 500);
        }
    }

    public function modifier_un_utilisateur(Request $request, string $id)
    {
        
        $autorisation_pseudo = false;
        $autorisation_email = false;

        $photo = $request->hasFile("image");

        $user = DB::table('users')->where('id', $id)->first();
        $user_existe = DB::table('users')->where('id', $id)->exists();
        
        if($user_existe){

            $pseudo_existe = DB::table('users')->where('pseudo', $request->pseudo)->exists();

            if(!$pseudo_existe){
                $autorisation_pseudo = true;
            }else{
                $verifier_pseudo = DB::table('users')->where('pseudo', $request->pseudo)->first();

                if(($verifier_pseudo->pseudo == $user->pseudo) && ($verifier_pseudo->email == $user->email)){
                    $autorisation_pseudo = true;
                }
            }
            if($autorisation_pseudo){
                $email_exites = DB::table('users')->where('email', $request->email)->exists();
                
                if($email_exites){
                    $verifier_email = DB::table('users')->where('email', $request->email)->first();
                    if($verifier_email->email == $user->email){
                        $autorisation_email = true;
                    }
                }else{
                    $autorisation_email = true;
                }

                if($autorisation_email){
                    if($photo){
                        $file = $request->file('image');
                        $extension = $file->getClientOriginalExtension();
                        $filename = time() . '.' .$extension;
                        $file->move("uploads/users/", $filename);
                        $image = 'uploads/users/'.$filename;
                    }else{
                        $image = null;
                    }
    
                    DB::table('users')->where('id', $id)->update([
                        'image' => $image,
                        'pseudo' => $request->pseudo,
                        'email' => $request->email,
                        'roles' => $request->roles
                    ]);
                    
                    return response()->json([
                        'status' => 200,
                        'message' => 'Modification effectuée!',
                    ]);
                }else{
                    return response()->json([
                        'status' => 404,
                        'message' => 'Email existe déjà !'
                    ]);    
                }
            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'Pseudo existe déjà !'
                ]);
            }
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat !',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function supprimer_un_utilisateur(string $id)
    {
        try {
            $user = DB::table('users')->where('id', $id)->first();
            if ($user) {
                DB::table('users')->where('id', $id)->delete();
                return response()->json([ 
                    'message' => 'Suppression effectuée avec succès',
                    'status' => 200]);
                } else {
                    return response()->json(['message' => 'Utilisateur non trouvé', 'status' => 404], 404);
                }
            } catch (\Exception $e) {   
                // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
                return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500]);
            }
    }
}
