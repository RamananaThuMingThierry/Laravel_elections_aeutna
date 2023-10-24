<?php

namespace App\Http\Controllers\API;

use DateTime;
use App\Models\electeurs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Date;
use function PHPUnit\Framework\isEmpty;

class ElecteursController extends Controller
{
    /*
     ** Display a listing of the resource.
    */

    public function resultat()
    {
        $MembresAEUTNA = electeurs::where('numero_carte', '<>', null)->get();
        $ElecteursMembres = electeurs::where('numero_carte', '<>', null)->where('status', 1)->get();
        $ElecteursNonAdheres = electeurs::where('numero_carte', null)->where('status', 1)->get();
        $ElecteursVotes = electeurs::where('status', 1)->get();
        
        return response()->json([
            'status' => 200,
            'MembresAEUTNA' => $MembresAEUTNA->count(),
            'ElecteursMembres' => $ElecteursMembres->count(),
            'ElecteursNonAdheres' => $ElecteursNonAdheres->count(),
            'Electeursvotes' => $ElecteursVotes->count()
        ]);
    }

    public function membres()
    {
        $membres = electeurs::orderBy('numero_carte', 'desc')->where('numero_carte', '<>', null)
            ->where('status', 0)
            ->get();
        return response()->json([
            'status' => 200,
            'nombres_membres' => $membres->count(),
            'electeurs_membres' => $membres
        ]);
    }

    
    public function non_adhere()
    {
        $non_adhere = electeurs::orderBy('nom', 'desc')->where('numero_carte', null)
            ->where('status', 1)
            ->get();

        return response()->json([
            'status' => 200,
            'nombres_non_adhere' => $non_adhere->count(),
            'electeurs_non_adhere' => $non_adhere
        ]);
    }
    public function recherche_membres(string $propriete, string $value){ 
        
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
            $membres = electeurs::where($propriete, $value)->where('status', 1)->get();
            if($membres->count() != 0){
                return response()->json([
                    'status' => 400,
                    'message' => 'Vous avez déjà votes !'
                ]); 
            }else{
                $membres = electeurs::where($propriete, $value)->where('status', 0)->get();    
            }
        }else{

            $membres = electeurs::where($propriete,'like',"%$value%")->where('status', 1)->get();

            if($membres->count() != 0){
                return response()->json([
                    'status' => 400,
                    'message' => 'Vous avez déjà votes !'
                ]); 
            }else{
                $membres = electeurs::where($propriete,'like', "%$value%")->where('status', 0)->get();    
            }
        }

        if($membres->count() != 0){
             return response()->json([
                 'status' => 200,
                 'recherche_membres' => $membres
             ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat !'
            ]);
        }
    }
 
    public function recherche_membre_electeurs(string $propriete, string $value){ 
        
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

            $electeur = electeurs::where($propriete,'like',"%$value%")->where('status', 0)->get();

            if($electeur->count() != 0){
                return response()->json([
                    'status' => 400,
                    'message' => 'Vous n\'avez pas encore votes !'
                ]); 
            }else{
                $electeur = electeurs::where($propriete,'like', "%$value%")->where('status', 1)->get();    
            }
        }

        if($electeur->count() != 0){
             return response()->json([
                 'status' => 200,
                 'recherche_membre_electeurs' => $electeur
             ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat !'
            ]);
        }
    }

    public function recherche_electeurs_non_adhere(string $propriete, string $value){ 
        
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

    public function liste_des_electeurs_membres()
    {
        $liste_des_electeurs_membres = electeurs::where('numero_carte', '<>', null)
                ->where('status', 1)
                ->get();

        return response()->json([
            'status' => 200,
            'nombres_liste_des_electeurs_membres' => $liste_des_electeurs_membres->count(),
            'liste_des_electeurs_membres' => $liste_des_electeurs_membres
        ]);
    }

    public function liste_des_electeurs_non_adhere()
    {
        $liste_des_electeurs_non_adhere = electeurs::where('numero_carte', null)
                ->where('status', 1)
                ->get();

        return response()->json([
            'status' => 200,
            'nombres_liste_des_electeurs_non_adhere' => $liste_des_electeurs_non_adhere->count(),
            'liste_des_electeurs_non_adhere' => $liste_des_electeurs_non_adhere
        ]);
    }

    public function liste_des_electeurs()
    {
        $electeurs = electeurs::where('status', 1)->get();
        return response()->json([
            'status' => 200,
            'nombres_electeurs' => $electeurs->count(),
            'electeurs' => $electeurs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $photo = $request->hasFile("photo");
        $numero_carte = $request->numero_carte;
        $nom = $request->nom;
        $prenom = $request->prenom;
        $ddn = $request->ddn;
        $ldn = $request->ldn;
        $sexe = $request->sexe;
        $cin = $request->cin;
        $delivrance = $request->delivrance;
        $filieres = $request->filieres;
        $niveau = $request->niveau;
        $adresse = $request->adresse;
        $contact = $request->contact;
        $axes = $request->axes;
        $sympathisant = $request->sympathisant;
        $facebook = $request->facebook;
        $date_inscription = $request->date_inscription;
        
        $existes = DB::table('electeurs')->where('numero_carte', $request->numero_carte)
                  ->exists();

        if(!$existes){               
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
                'ddn' => $ddn,
                'ldn' => $ldn,
                'sexe' => $sexe,
                'cin' => $cin,
                'delivrance_cin' => $delivrance,
                'filieres' => $filieres,
                'niveau' => $filieres,
                'niveau' => $niveau,
                'adresse' => $adresse,
                'contact' => $contact,
                'axes' => $axes,
                'sympathisant' => $sympathisant,
                'facebook' => $facebook,
                'date_inscription' => $date_inscription
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Enregistrement effectué !',
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Votre numéro de carte existe déjà !',
            ]); 
        }
    }

    public function nouveau_bachelier(Request $request)
    {
        $nom = $request->nom;
        $prenom = $request->prenom;
        $cin = $request->cin;
        $sexe = $request->sexe;
        $ddn = $request->ddn;
        $ldn = $request->ldn;
        $delivrance = $request->delivrance;
        $adresse = $request->adresse;
        $contact = $request->contact;
        $facebook = $request->facebook;
        $axes = $request->axes;
        $votes = 'Convocation';
        $sympathisant = 'Non';
        $status = 1;
        $secteurs = $request->secteurs;
        $date_inscription = now();

        DB::table('electeurs')->insert([
            'nom' => $nom,
            'prenom' => $prenom,
            'ddn' => $ddn,
            'ldn' => $ldn,
            'sexe' => $sexe,
            'cin' => $cin,
            'delivrance_cin' => $delivrance,
            'adresse' => $adresse,
            'contact' => $contact,
            'axes' => $axes,
            'votes' => $votes,
            'status' => $status,
            'secteurs' => $secteurs,
            'sympathisant' => $sympathisant,
            'facebook' => $facebook,
            'date_inscription' => $date_inscription
        ]);

        
        return response()->json([
            'status' => 200,
            'message' => 'Enregistrement effectué !',
        ]);
    }

    public function valide_membres_electeurs(Request $request, string $id){
       
        try {
            $electeur = DB::table('electeurs')->where('id', $id)->first();
   
            
             if ($electeur) {
                
                DB::table('electeurs')->where('id', $id)->update([
                    'secteurs' => $request->secteurs,
                    'status' => 1,
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
    
    /**
     * Display the specified resource.
     */
    public function show(string $electeur_id)
    {
        try {
    $electeur = DB::table('electeurs')->where('id', $electeur_id)->first();
            if ($electeur) {
    return response()->json(['electeur' => $electeur, 'status' => 200], 200);
            } else {
                return response()->json(['message' => 'Électeur non trouvé', 'status' => 404], 404);
            }
        } catch (\Exception $e) {
            
           
    // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
            return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500], 500);
        }
    }

    public function desapprouve_membre_electeur(string $id)
    {
        $electeur = DB::table('electeurs')->where('id', $id)->first();
        
        if($electeur){
            DB::table('electeurs')->where('id', $id)->update([
                'secteurs' => null,
                'status' => 0,
                'votes' => null
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Il a été désapprouvé !'
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat trouvé !'
            ]);
        }
    }

    public function edit(string $id)
    {
        try {
            $electeur = DB::table('electeurs')->where('id', $id)->first();
                    if ($electeur) {
            return response()->json(['membre' => $electeur, 'status' => 200], 200);
                    } else {
                        return response()->json(['message' => 'Électeur non trouvé', 'status' => 404], 404);
                    }
                } catch (\Exception $e) {
                    
                    
            // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
                    return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500], 500);
                }
    }

    public function approuve_membres(string $id)
    {
        try {
        $electeur = DB::table('electeurs')->where('id', $id)->first();
                if ($electeur) {
        return response()->json(['electeur' => $electeur, 'status' => 200], 200);
                } else {
                    return response()->json(['message' => 'Électeur non trouvé', 'status' => 404], 404);
                }
            } catch (\Exception $e) {
                
                
        // Gérez l'erreur et renvoyez une réponse d'erreur appropriée
                return response()->json(['message' => 'Une erreur interne s\'est produite.', 'status' => 500], 500);
            }
    }

    /**
     * Update the specified resource in storage.
     */


    public function update(Request $request, string $id)
    {    
        $autorisation = false;

        $photo = $request->hasFile("photo");
        $electeur = DB::table('electeurs')->where('id', $id)->first();


        if($electeur){

            $existes =  DB::table('electeurs')->where('numero_carte', $request->numero_carte)->first();

            if($existes){
                if($electeur->numero_carte == $existes->numero_carte){
                    $autorisation = true;
                }
            }

            if($autorisation){
                
                if($photo){
                    $file = $request->file('photo');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' .$extension;
                    $file->move("uploads/electeurs/", $filename);
                    $image = 'uploads/electeurs/'.$filename;
                }else{
                    $image = null;
                }
                
                $numero_carte = $request->numero_carte;
                $nom = $request->nom;
                $prenom = $request->prenom;
                $ddn = $request->ddn;
                $ldn = $request->ldn;
                $sexe = $request->sexe;
                $cin = $request->cin;
                $delivrance = $request->delivrance;
                $filieres = $request->filieres;
                $niveau = $request->niveau;
                $adresse = $request->adresse;
                $contact = $request->contact;
                $axes = $request->axes;
                $sympathisant = $request->sympathisant;
                $facebook = $request->facebook;
                $date_inscription = $request->date_inscription;

                DB::table('electeurs')->where('id', $id)->update([
                    'photo' => $image,
                    'numero_carte' => $numero_carte,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'ddn' => $ddn,
                    'ldn' => $ldn,
                    'sexe' => $sexe,
                    'cin' => $cin,
                    'delivrance_cin' => $delivrance,
                    'filieres' => $filieres,
                    'niveau' => $filieres,
                    'niveau' => $niveau,
                    'adresse' => $adresse,
                    'contact' => $contact,
                    'axes' => $axes,
                    'sympathisant' => $sympathisant,
                    'facebook' => $facebook,
                    'date_inscription' => $date_inscription
                ]);
                
                return response()->json([
                    'status' => 200,
                    'message' => 'Modification effectuée!',
                ]);
            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'Ce numéro de carte appartient à une autre membre !',
                ]);
            }
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Électeur non trouvé !'
            ]);
        }
    }
   
    public function update_electeur_non_adhere(Request $request, string $id)
    {    
        
        $electeur = DB::table('electeurs')->where('id', $id)->first();

        if($electeur){
            $nom = $request->nom;
            $prenom = $request->prenom;
            $ddn = $request->ddn;
            $ldn = $request->ldn;
            $sexe = $request->sexe;
            $cin = $request->cin;
            $delivrance = $request->delivrance_cin;
            $adresse = $request->adresse;
            $contact = $request->contact;
            $axes = $request->axes;
            $facebook = $request->facebook;
            $secteurs = $request->secteurs;
            $date_inscription = $request->date_inscription;
            
            DB::table('electeurs')->where('id', $id)->update([
                'nom' => $nom,
                'prenom' => $prenom,
                'ddn' => $ddn,
                'ldn' => $ldn,
                'sexe' => $sexe,
                'cin' => $cin,
                'delivrance_cin' => $delivrance,
                'adresse' => $adresse,
                'contact' => $contact,
                'axes' => $axes,
                'secteurs' => $secteurs,
                'facebook' => $facebook,
                'date_inscription' => $date_inscription
            ]);
            
            return response()->json([
                'status' => 200,
                'message' => 'Modification effectuée!',
            ]);
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
