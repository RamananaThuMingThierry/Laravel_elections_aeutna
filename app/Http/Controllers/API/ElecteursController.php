<?php

namespace App\Http\Controllers\API;

use App\Models\electeurs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Support\Facades\Date;

use function PHPUnit\Framework\isEmpty;

class ElecteursController extends Controller
{
    /**
     * Display a listing of the resource.
     */


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

    public function recherche_membres(string $propriete, string $value){ 
        
        $bool = false;
        if($propriete == 'numero_carte'){
            $bool = true;
        }else if($propriete == 'cin'){
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


        $electeur = new electeurs();

        $existes = electeurs::where('numero_carte', $request->numero_carte)
                  ->exists();
                  
        // Vérifier si cet électeurs exists dans la base de données
        if(!$existes){               
            if($photo){
                $file = $request->file('photo');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' .$extension;
                $file->move("uploads/electeurs/", $filename);
                $electeur->photo = 'uploads/electeurs/'.$filename;
            }else{
                $electeur->photo = null;
            }
            $electeur->numero_carte = $numero_carte;
            $electeur->nom = $nom;
            $electeur->prenom = $prenom;
            $electeur->ddn = $ddn;
            $electeur->ldn = $ldn;
            $electeur->sexe = $sexe;
            $electeur->cin = $cin;
            $electeur->delivrance_cin = $delivrance;
            $electeur->filieres = $filieres;
            $electeur->niveau = $niveau;
            $electeur->adresse = $adresse;
            $electeur->contact = '0'.$contact;
            $electeur->axes = $axes;
            $electeur->sympathisant = $sympathisant ?? 'Non';
            $electeur->facebook = $facebook;
            $electeur->date_inscription = $date_inscription;
            $electeur->save();
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
        
        $electeur = new electeurs();

        $existes = electeurs::where('numero_carte', $request->numero_carte)
            ->whereNotNull('numero_carte')      
            ->exists();
                  
        // Vérifier si cet électeurs exists dans la base de données
        if(!$existes){
          
            if($request->numero_carte == null){
                if($request->votes == 'releve'){
                    if($request->status != 1){
                        return response()->json([
                            'status' => 404,
                            'message' => 'Veuillez marque l\'élut !',
                        ]);
                    }
                    if($request->secteurs == null){
                        return response()->json([
                            'status' => 404,
                            'message' => 'Veuillez séléctionner votre domicile !',
                        ]);
                    }
                    $electeur->votes = $request->votes;
                    $electeur->status = 1;
                }else{
                    return response()->json([
                        'status' => 404,
                        'message' => 'Ce statut n\'a pas encore le numéro de carte !',
                    ]);
                }
            }
            
            if($request->hasFile("photo")){
                $file = $request->file('photo');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' .$extension;
                $file->move("uploads/electeurs/", $filename);
                $electeur->photo = 'uploads/electeurs/'.$filename;
            }else{
                $electeur->photo = null;
            }
            
            $electeur->numero_carte = $request->numero_carte;
            $electeur->nom = $request->nom;
            $electeur->prenom = $request->prenom;
            $electeur->cin = $request->cin;
            $electeur->delivrance_cin = $request->delivrance_cin;
            $electeur->adresse = $request->adresse;
            $electeur->contact = $request->contact;
            $electeur->axes = $request->axes;
            $electeur->date_inscription = now();
            $electeur->save();
            return response()->json([
                'status' => 200,
                'message' => 'Enregistrement effectué !',
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Ce numéro matricule existe déjà !',
            ]); 
        }
    }

    public function valide_membres_electeurs(Request $request, string $id){
       
        $electeur =  electeurs::find($id);
   
        if($electeur){

            $electeur->secteurs = $request->secteurs;
            $electeur->status = 1;
            $electeur->votes = $request->votes;
            $electeur->save();
            
            return response()->json([
                'status' => 200,
                'message' => 'Opération effectuée!',
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Électeur non trouvé !'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $electeur = electeurs::find($id);
        
        if($electeur){
            return response()->json([
                'status' => 200,
                'electeur' => $electeur
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
        $membre = electeurs::find($id);
        
        if($membre){
            return response()->json([
                'status' => 200,
                'membre' => $membre
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat trouvé !'
            ]);
        }
    }

    public function approuve_membres(string $id)
    {
        $electeur = electeurs::find($id);
        
        if($electeur){
            return response()->json([
                'status' => 200,
                'electeur' => $electeur
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat trouvé !'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */


    public function update(Request $request, string $id)
    {
        $autorisation = false;

        $electeur =  electeurs::find($id);
   
        dd($request->numero_carte);

        if($electeur){

            $existes = electeurs::where('numero_carte', $request->numero_carte)->get();
            var_dump($existes);
            if($existes){
                if($electeur->numer_carte == $existes->numero_carte){
                    $autorisation = true;
                }
            }

            if($autorisation){
                
                if($request->photo != null){
                        if($request->hasFile("photo")){
                        $file = $request->file('photo');
                        $extension = $file->getClientOriginalExtension();
                        $filename = time() . '.' .$extension;
                        $file->move("uploads/electeurs/", $filename);
                        $electeur->photo = 'uploads/electeurs/'.$filename;
                    }
                }

                $electeur->numero_carte = $request->numero_carte;
                $electeur->nom = $request->nom;
                $electeur->prenom = $request->prenom;
                $electeur->ddn = $request->ddn;
                $electeur->ldn = $request->ldn;
                $electeur->sexe = $request->sexe;
                $electeur->cin = $request->cin;
                $electeur->delivrance_cin = $request->delivrance_cin;
                $electeur->filieres = $request->filieres;
                $electeur->niveau = $request->niveau;
                $electeur->adresse = $request->adresse;
                $electeur->contact = $request->contact;
                $electeur->axes = $request->axes;
                $electeur->sympathisant = $request->sympathisant;
                $electeur->facebook = $request->facebook;
                $electeur->date_inscription = $request->date_inscription;
                $electeur->save();
                
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $electeur = electeurs::find($id);
        
        if($electeur){
            
            $electeur->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Suppression effectuée avec succès'
            ]);
        }else{
            return response()->json([
                'status' => 404,
                'message' => 'Aucun résultat trouvé !'
            ]);
        }
    }
}
