<?php

namespace App\Http\Controllers\API;

use DateTime;
use PDF;
use App\Models\electeurs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ElecteursController extends Controller
{
    public function statistiques()
    {

        $MembresAEUTNA = electeurs::where('numero_carte', '<>', null)->get();
        $ElecteursMembres = electeurs::where('numero_carte', '<>', null)->where('status', 1)->get();
        $ElecteursNonAdheres = electeurs::where('numero_carte', null)->where('status', 1)->get();
        $ElecteursVotes = electeurs::where('status', 1)->get();
        
        $nombre_67h = electeurs::where('secteurs', '67 h')->where('status', 1)->get();
        $nombre_Amphipo = electeurs::where('secteurs', 'Ambohipo')->where('status', 1)->get();
        $nombre_Ambolikandrina = electeurs::where('secteurs', 'Ambolikandrina')->where('status', 1)->get();
        $nombre_Ankatso_1 = electeurs::where('secteurs', 'Ankatso 1')->where('status', 1)->get();
        $nombre_Ankatso_2 = electeurs::where('secteurs', 'Ankatso 2')->where('status', 1)->get();
        $nombre_Centre_Ville = electeurs::where('secteurs', 'Centre Ville')->where('status', 1)->get();
        $nombre_Itaosy = electeurs::where('secteurs', 'Itaosy')->where('status', 1)->get();
        $nombre_Ivato = electeurs::where('secteurs', 'Ivato')->where('status', 1)->get();
        $nombre_Votovorona = electeurs::where('secteurs', 'Votovorona')->where('status', 1)->get();
        
        $nombre_cin = electeurs::where('votes' , 'C.I.N')->where('status', 1)->get();
        $nombre_copie = electeurs::where('votes' , 'Copie')->where('status', 1)->get();
        $nombre_releve_de_notes = electeurs::where('votes' , 'Relève de notes')->where('status', 1)->get();
        
        $nombre_carte_aeutna = electeurs::where('votes', 'Carte AEUTNA')->where('status', 1)->get();
        $nombre_nouveau_adhere = electeurs::where('adhesion', 1)->where('status', 1)->get();

        return response()->json([
            'status' => 200,
            'nombre_carte_aeutna' => $nombre_carte_aeutna->count(),
            'MembresAEUTNA' => $MembresAEUTNA->count(),
            'ElecteursMembres' => $ElecteursMembres->count(),
            'ElecteursNonAdheres' => $ElecteursNonAdheres->count(),
            'Electeursvotes' => $ElecteursVotes->count(),
            'nombre_67h' => $nombre_67h->count(),
            'nombre_Ambohipo' => $nombre_Amphipo->count(),
            'nombre_Ambolikandrina' => $nombre_Ambolikandrina->count(),
            'nombre_Ankatso_1' => $nombre_Ankatso_1->count(),
            'nombre_Ankatso_2' => $nombre_Ankatso_2->count(),
            'nombre_Centre_Ville' => $nombre_Centre_Ville->count(),
            'nombre_Itaosy' => $nombre_Itaosy->count(),
            'nombre_Ivato' => $nombre_Ivato->count(),
            'nombre_Votovorona' => $nombre_Votovorona->count(),
            'nombre_cin' => $nombre_cin->count(),
            'nombre_copie' => $nombre_copie->count(),
            'nombre_releve_de_notes' => $nombre_releve_de_notes->count(),
            'nombre_nouveau_adhere' => $nombre_nouveau_adhere->count(),
        ]);

    }

    public function liste_des_electeurs_membres()
    {
        $liste_des_electeurs_membres = electeurs::orderBy('nom', 'asc')->where('numero_carte', '<>', null)
            ->where('status', 0)
            ->get();
        return response()->json([
            'status' => 200,
            'liste_des_electeurs_membres' => $liste_des_electeurs_membres
        ]);
    }
    
    public function liste_des_electeurs_non_adheres()
    {
        $liste_des_electeurs_non_adhere = electeurs::orderBy('nom', 'desc')->where('numero_carte', null)
            ->where('status', 1)
            ->get();

        return response()->json([
            'status' => 200,
            'liste_des_electeurs_non_adhere' => $liste_des_electeurs_non_adhere
        ]);
    }
    
    public function recherche_un_electeur_membre(string $propriete, string $value){ 
        
        $autorisations = false;

        if($propriete == 'numero_carte'){
            $autorisations = true;
        }else if($propriete == 'cin'){
            if(strlen($value) != 12){
                return response()->json([
                    'status' => 400,
                    'message' => 'C.I.N invalide !'
                ]); 
            }
            $autorisations = true;
        }

        if($autorisations){
            $recherche_un_electeur_membre = electeurs::where($propriete, $value)->where('numero_carte', '<>', null)->where('status', 1)->get();
            if($recherche_un_electeur_membre->count() != 0){
                return response()->json([
                    'status' => 400,
                    'message' => 'Vous avez déjà votes !'
                ]); 
            }else{
                $recherche_un_electeur_membre = electeurs::where($propriete, $value)->where('numero_carte', '<>', null)->where('status', 0)->get();    
            }
        }else{

            $recherche_un_electeur_membre = electeurs::where($propriete,'like',"%$value%")->where('numero_carte', '<>', null)->where('status', 1)->get();

            // if($recherche_un_electeur_membre->count() != 0){
            //     return response()->json([
            //         'status' => 400,
            //         'message' => 'Vous avez déjà votes !'
            //     ]); 
            // }else{
                $recherche_un_electeur_membre = electeurs::where($propriete,'like', "%$value%")->where('numero_carte', '<>', null)->where('status', 0)->get();    
            // }
        }

        if($recherche_un_electeur_membre->count() != 0){
             return response()->json([
                 'status' => 200,
                 'recherche_un_electeur_membre' => $recherche_un_electeur_membre
             ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Vérifiez s\'il avait déjà vote'
            ]);
        }
    }
 
    public function recherche_electeur_non_adhere(string $propriete, string $value){ 
        
        $autorisations = false;
        
        if($propriete == 'cin'){
            if(strlen($value) != 12){
                return response()->json([
                    'status' => 400,
                    'message' => 'C.I.N invalide !'
                ]); 
            }
            $autorisations = true;
        }

        if($autorisations){
            $electeur = electeurs::where($propriete, $value)->where('numero_carte', null)->where('status', 1)->get();
        }else{
            $electeur = electeurs::where($propriete,'like',"%$value%")->where('numero_carte', null)->where('status', 1)->get();
        }
        if($electeur->count() != 0){
             return response()->json([
                 'status' => 200,
                 'recherche_electeur_non_adhere' => $electeur
             ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat !'
            ]);
        }
    }

    public function recherche_un_electeur_vote(string $propriete, string $value){ 
        
        $bool = false;
        if($propriete == 'numero_carte'){
            $bool = true;
        }else if($propriete == 'cin'){
            if(strlen($value) != 12){
                return response()->json([
                    'status' => 400,
                    'message' => 'C.I.N invalide !'
                ]); 
            }
            $bool = true;
        }

        if($bool){
            $electeur = electeurs::where($propriete, $value)->where('status', 0)->get();
            if($electeur->count() != 0){
                return response()->json([
                    'status' => 400,
                    'message' => 'Vous n\'avez pas encore votes !'
                ]); 
            }else{
                $electeur = electeurs::where($propriete, $value)->where('status', 1)->get();    
            }
        }else{

            // $electeur = electeurs::where($propriete,'like',"%$value%")->where('status', 0)->get();

            // if($electeur->count() != 0){
            //     return response()->json([
            //         'status' => 400,
            //         'message' => 'Vous n\'avez pas encore votes !'
            //     ]); 
            // }else{
                $electeur = electeurs::where($propriete,'like', "%$value%")->where('status', 1)->get();    
            // }
        }

        if($electeur->count() != 0){
             return response()->json([
                 'status' => 200,
                 'recherche_un_electeur_vote' => $electeur
             ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat !'
            ]);
        }
    }

    public function recherche_electeur_non_adhere_votes(string $propriete, string $value){ 
        
        $bool = false;
        if($propriete == 'cin'){
            if(strlen($value) != 12){
                return response()->json([
                    'status' => 400,
                    'message' => 'C.I.N invalide !'
                ]); 
            }
            $bool = true;
        }

        if($bool){
            $electeur = electeurs::where($propriete, $value)->where('status', 1)->get();
        }else{
            $electeur = electeurs::where($propriete,'like',"%$value%")->where('status', 1)->get();
        }
        if($electeur->count() != 0){
             return response()->json([
                 'status' => 200,
                 'recherche_electeur_non_adhere' => $electeur
             ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat !'
            ]);
        }
    }

    public function liste_des_electeurs_votes()
    {
        $electeurs_votes = electeurs::orderBy('heure_vote', 'desc')->where('status', 1)->get();
        return response()->json([
            'status' => 200,
            'electeurs_votes' => $electeurs_votes
        ]);
    }

    public function ajouter_un_electeur_membre(Request $request)
    {
        $photo = $request->hasFile("photo");
        $numero_carte = $request->numero_carte;
        $nom = $request->nom;
        $prenom = $request->prenom ?? '';
        $sexe = $request->sexe;
        $cin = $request->cin;
        $axes = $request->axes;
        $sympathisant = $request->sympathisant;
        $date_inscription = $request->date_inscription;

        $concatenation_nom_prenom = $nom .' '. $prenom;

        $verifier_conctatenation_nom_prenom = DB::table('electeurs')
            ->select('*')
            ->whereRaw('CONCAT(nom, " ", prenom) = ?', [$concatenation_nom_prenom])
            ->exists();
            
        if(!$verifier_conctatenation_nom_prenom){
            if($cin == null){
                $verifier_cin = false;
            }else{
                $verifier_cin =  DB::table('electeurs')
                ->where('cin', $cin)
                ->exists();    
            }
            
            if(!$verifier_cin){
                if($photo){
                    $file = $request->file('photo');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' .$extension;
                    $file->move("uploads/electeurs/", $filename);
                    $image = 'uploads/electeurs/'.$filename;
                }else{
                    $image = null;
                }
    
                DB::table('electeurs')->insert([
                    'photo' => $image,
                    'numero_carte' => $numero_carte,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'sexe' => $sexe,
                    'cin' => $cin,
                    'axes' => $axes,
                    'sympathisant' => $sympathisant,
                    'date_inscription' => $date_inscription
                ]);
    
                return response()->json([
                    'status' => 200,
                    'message' => 'Enregistrement effectué !',
                ]);
            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'C.I.N existe déjà !',
                ]); 
            }
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'L\'électeur existe déjà dans la base de données!'
            ]);
        }     
    }

    public function ajouter_un_electeur_non_adhere(Request $request)
    {
        $nom = $request->nom;
        $prenom = $request->prenom;
        $cin = $request->cin;
        $sexe = $request->sexe;
        $axes = $request->axes;
        $votes = $request->votes;
        $sympathisant = 'Non';
        $status = 1;
        $secteurs = $request->secteurs;
        $date_inscription = now();
        $adhesion = $request->adhesion;

        $concatenation_nom_prenom = $nom.' '.$prenom;

        $verifier_conctatenation_nom_prenom = DB::table('electeurs')
            ->select('*')
            ->whereRaw('CONCAT(nom, " ", prenom) = ? ', [$concatenation_nom_prenom])
            ->exists();

        if(!$verifier_conctatenation_nom_prenom){
            if($cin == null){
                $verifier_cin = false;
            }else{
                $verifier_cin =  DB::table('electeurs')
                ->where('cin', $cin)
                ->exists();    
            }

            if($verifier_cin){
                return response()->json([
                    'status' => 400,
                    'message' => 'Votre C.I.N appartient à un autre membre',
                ]); 
            }else{
                DB::table('electeurs')->insert([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'sexe' => $sexe,
                    'cin' => $cin,
                    'axes' => $axes,
                    'votes' => $votes,
                    'status' => $status,
                    'secteurs' => $secteurs,
                    'heure_vote' => Carbon::now()->addHour(3),
                    'sympathisant' => $sympathisant,
                    'adhesion' => $adhesion,
                    'date_inscription' => $date_inscription
                ]);
                
                return response()->json([
                    'status' => 200,
                    'message' => 'Enregistrement effectué !',
                ]);
            }
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Désolé ! Vous êtes déjà membres!'
            ]);
        }

    }

    public function valide_un_electeur_membre(Request $request, string $id){
       
        try {
            
            $electeur_membre = DB::table('electeurs')->where('id', $id)->first();

             if ($electeur_membre) {
                
                DB::table('electeurs')->where('id', $id)->update([
                    'secteurs' => $request->secteurs,
                    'status' => 1,
                    'heure_vote' => Carbon::now()->addHour(3),
                    'votes' => $request->votes
                ]);
                
                return response()->json([
                    'status' => 200,
                    'message' => 'Opération effectuée !'
                ]);

                } else {
                    return response()->json(['message' => 'Électeur non trouvé', 'status' => 404]);
                }

            } catch (\Exception $e) {
                return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500], 500);
            }
    }

    public function afficher_un_electeur_non_adhere(string $id)
    {
        try {
        $electeur_non_adhere = DB::table('electeurs')->where('id', $id)->where('numero_carte',null)->where('status', 1)->first();
        
        if ($electeur_non_adhere) {
        return response()->json(['electeur_non_adhere' => $electeur_non_adhere, 'status' => 200], 200);
            } else {
                return response()->json(['message' => 'Électeur non trouvé', 'status' => 404], 404);
            }
        } catch (\Exception $e) {   
        // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
            return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500], 500);
        }
    }
   
    public function afficher_un_electeur_non_adhere_vote(string $id)
    {
        try {
        $electeur_non_adhere_vote = DB::table('electeurs')->where('id', $id)->where('numero_carte',null)->where('status', 1)->first();
        
        if ($electeur_non_adhere_vote) {
        return response()->json(['electeur_non_adhere_vote' => $electeur_non_adhere_vote, 'status' => 200], 200);
            } else {
                return response()->json(['message' => 'Électeur non trouvé', 'status' => 404], 404);
            }
        } catch (\Exception $e) {   
        // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
            return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500], 500);
        }
    }

    public function afficher_un_electeur_membre(string $id)
    {
        try {
        $electeur_membre = DB::table('electeurs')->where('id', $id)->first();
            if ($electeur_membre) {
                return response()->json(['electeur_membre' => $electeur_membre, 'status' => 200], 200);
            } else {
                return response()->json(['message' => 'Électeur non trouvé', 'status' => 404], 404);
            }
        } catch (\Exception $e) {   
            // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
            return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500], 500);
        }
    }
  
    public function afficher_un_electeur_membre_vote(string $id)
    {
        try {
             $electeur_membre_vote = DB::table('electeurs')->where('id', $id)->first();
            if ($electeur_membre_vote) {
                return response()->json(['electeur_membre_vote' => $electeur_membre_vote, 'status' => 200], 200);
            } else {
                return response()->json(['message' => 'Électeur non trouvé', 'status' => 404], 404);
            }
        } catch (\Exception $e) {   
            // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
            return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500], 500);
        }
    }

    public function desapprouve_un_electeur_vote(string $id)
    {
        $electeur_membre_vote = DB::table('electeurs')->where('id', $id)->first();
        
        if($electeur_membre_vote){
            DB::table('electeurs')->where('id', $id)->update([
                'secteurs' => null,
                'status' => 0,
                'heure_vote' => null,
                'votes' => null
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Vous avez été désapprouvé !'
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat trouvé !'
            ]);
        }
    }

    public function obtenir_un_electeur(string $id)
    {
        try {
            $electeur = DB::table('electeurs')->where('id', $id)->first();
                    if ($electeur) {
            return response()->json(['electeur_membre' => $electeur, 'status' => 200], 200);
                    } else {
                        return response()->json(['message' => 'Électeur non trouvé', 'status' => 404], 404);
                    }
                } catch (\Exception $e) {
                    
                    
            // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
                    return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500], 500);
                }
    }

    public function obtenir_un_electeur_non_adhere(string $id)
    {
        try {
            $electeur = DB::table('electeurs')->where('id', $id)->first();
                    if ($electeur) {
            return response()->json(['electeur_non_adhere' => $electeur, 'status' => 200], 200);
                    } else {
                        return response()->json(['message' => 'Électeur non trouvé', 'status' => 404], 404);
                    }
                } catch (\Exception $e) {
                    
                    
            // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
                    return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500], 500);
                }
    }

    public function approuve_un_electeur_membre(string $id)
    {
        try {
            $electeur_membre = DB::table('electeurs')->where('id', $id)->first();
                    if ($electeur_membre) {
            return response()->json(['electeur_membre' => $electeur_membre, 'status' => 200], 200);
                    } else {
                        return response()->json(['message' => 'Électeur non trouvé', 'status' => 404], 404);
                    }
        } catch (\Exception $e) {
            // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
            return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500], 500);
        }
    }

    public function modifier_un_electeur_membre(Request $request, string $id)
    {    
        $autorisations_numero_carte = false;
        $autorisations_nom_prenom = false;
        $autorisations_cin = false;
        
        $sexe = $request->sexe;
        $numero_carte = $request->numero_carte;
        $nom = $request->nom;
        $prenom = $request->prenom ?? '';
        $cin = $request->cin;
        $axes = $request->axes;
        $date_inscription = $request->date_inscription;
        $sympathisant = $request->sympathisant;

        $photo = $request->hasFile("photo");
        
        $electeur_existes = DB::table('electeurs')->where('id', $id)->exists();
        $electeur_membre = DB::table('electeurs')->where('id', $id)->first();

        if($electeur_existes){
            $existe_nom_prenom = DB::table('electeurs')->where('nom', $nom)->where('prenom', $prenom)->exists();

            if($existe_nom_prenom){
                $verifier_nom_prenom = DB::table('electeurs')->where('nom', $nom)->where('prenom', $prenom)->first();

                if(($verifier_nom_prenom->nom == $electeur_membre->nom) && $verifier_nom_prenom->prenom == $electeur_membre->prenom){
                    $autorisations_nom_prenom = true;
                }
            }else{
                $autorisations_nom_prenom = true;
            }


            if($autorisations_nom_prenom){

                if($cin == null){
                    $existe_cin = false;
                }else{
                    $existe_cin =  DB::table('electeurs')
                    ->where('cin', $cin)
                    ->exists();    
                }

                if(!$existe_cin){
                    $autorisations_cin = true;
                }else{

                    $verifier_cin = DB::table('electeurs')->where('cin', $cin)->first();

                    if($electeur_membre->cin == $verifier_cin->cin){
                        $autorisations_cin = true;
                    }
                }

                if($autorisations_cin){

                    if($photo){
                        
                        $file = $request->file('photo');
                        $extension = $file->getClientOriginalExtension();
                        $filename = time() . '.' .$extension;
                        $file->move("uploads/electeurs/", $filename);
                        $image = 'uploads/electeurs/'.$filename;
                        
                        DB::table('electeurs')->where('id', $id)->update([
                            'photo' => $image,
                            'numero_carte' => $numero_carte,
                            'nom' => $nom,
                            'prenom' => $prenom ?? '',
                            'sexe' => $sexe,
                            'cin' => $cin,
                            'axes' => $axes,
                            'sympathisant' => $sympathisant,
                            'date_inscription' => $date_inscription
                        ]);
                    }else{
                        DB::table('electeurs')->where('id', $id)->update([
                            'numero_carte' => $numero_carte,
                            'nom' => $nom,
                            'prenom' => $prenom ?? '',
                            'sexe' => $sexe,
                            'cin' => $cin,
                            'axes' => $axes,
                            'sympathisant' => $sympathisant,
                            'date_inscription' => $date_inscription
                        ]);
                        
                    }
                    return response()->json([
                        'status' => 200,
                        'message' => 'Modification effectuée!',
                    ]);

                }else{
                    return response()->json([
                        'status' => 404,
                        'message' => 'C.I.N appartient à un autre électeur !'
                    ]);    
                }
            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'Electeur existe déjà !'
                ]);    
            }
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat!'
            ]);
        }
    }
   
    public function modifier_un_electeur_non_adhere(Request $request, string $id)
    {    
        
        $autorisations_nom_prenom = false;
        $autorisations_cin = false;

        $nom = $request->nom;
        $prenom = $request->prenom ?? '';
        $sexe = $request->sexe;
        $cin = $request->cin;
        $secteurs = $request->secteurs;
        $axes = $request->axes;
        $votes = $request->votes;
        $adhesion = $request->adhesion;
        $date_inscription = $request->date_inscription;
        $heure_vote = Carbon::now()->addHour(3);
        $electeur = DB::table('electeurs')->where('id', $id)->first();
        $electeur_existes = DB::table('electeurs')->where('id', $id)->exists();

        if($electeur_existes){
            $existe_nom_prenom = DB::table('electeurs')->where('nom', $nom)->where('prenom', $prenom)->exists();

            if($existe_nom_prenom){
                $verifier_nom_prenom = DB::table('electeurs')->where('nom', $nom)->where('prenom', $prenom)->first();
                if(($verifier_nom_prenom->nom == $electeur->nom) && ($verifier_nom_prenom->prenom == $electeur->prenom)){
                    $autorisations_nom_prenom = true;
                }
            }else{
                $autorisations_nom_prenom = true;
            }

            if($autorisations_nom_prenom){

                if($cin == null){
                    $existe_cin = false;
                }else{
                    $existe_cin =  DB::table('electeurs')
                    ->where('cin', $cin)
                    ->exists();    
                }

                if($existe_cin){

                    $verifier_cin = DB::table('electeurs')->where('cin', $cin)->first();
                    
                    if($electeur->cin == $verifier_cin->cin){
                        $autorisations_cin = true;
                    }
                }else{
                    $autorisations_cin = true;
                }

                if($autorisations_cin){

                    DB::table('electeurs')->where('id', $id)->update([
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'sexe' => $sexe,
                        'cin' => $cin,
                        'axes' => $axes,
                        'votes' => $votes,
                        'heure_vote' => $heure_vote,
                        'secteurs' => $secteurs,
                        'adhesion' => $adhesion,
                        'date_inscription' => $date_inscription
                    ]);
                
                    return response()->json([
                        'status' => 200,
                        'message' => 'Modification effectuée!',
                    ]);
                }else{
                    return response()->json([
                        'status' => 404,
                        'message' => 'C.I.N appartient à un autre électeur !',
                    ]);    
                }

            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'L\'électeur existe déjà !',
                ]);
            }

        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat!',
            ]); 
        }
    }

    public function destroy(string $electeur_id)
    {
        try {
            $electeur = DB::table('electeurs')->where('id', $electeur_id)->first();
            if ($electeur) {
                DB::table('electeurs')->where('id', $electeur_id)->delete();
                return response()->json([ 
                    'message' => 'Suppression effectuée avec succès',
                    'status' => 200]);
                } else {
                    return response()->json(['message' => 'Électeur non trouvé', 'status' => 404], 404);
                }
            } catch (\Exception $e) {   
                // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
                return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500]);
            }
        }
    }
