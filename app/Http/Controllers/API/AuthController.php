<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Le champ d\'email est obligatoire.',
            'email.email' => 'Le champ d\'email doit être une adresse email valide.',
            'password.required' => 'Le champ de mot de passe est obligatoire.',
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation_errors' => $validator->messages(),
            ]);
        }else{
       
            $user = User::where('email', $request->email)->first();

            if(!$user || !Hash::check($request->password, $user->password)){
                return response()->json([
                    'status' => 401,
                    'message' => 'Informations d\'identification invalides',
                ]);

            }else{
                
                $token = $user->createToken($user->email.'_Token')->plainTextToken;
            
                return response()->json([
                    'status' => 200,
                    'pseudo' => $user->pseudo,
                    'token' => $token,
                    'message' => 'Connexion avec succès !',
                ]);
            }

            
        }
    }

    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'pseudo' => 'required|max:191',
            'email' => 'required|email|max:191|unique:users',
            'password' => 'required|min:8',
            'c_password' => 'required|min:8'
        ]);

        if($validator->fails()){
            return response()->json([
                'Validation_errors' => $validator->messages(),
            ]);
        }else{
            $user = User::create([
                'pseudo' => $request->pseudo,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $token = $user->createToken($user->email.'_Token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'pseudo' => $user->pseudo,
                'token' => $token,
                'message' => 'Inscription avec succès !',
            ]);
        }
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => "Déconnexion effectuée",
        ]);
    }
}
