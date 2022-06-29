<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AuthController extends Controller
{
    public function login(Request $request){

        // Validar
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        // Verificar
        $credenciales = request(["email","password"]);
        if(!Auth::attempt($credenciales)){
            return response()->json([
                "mensaje" => "Usuario no autorizado"
            ], 401);
        }
        

        // Generar token
        $usuario = $request->user();
        $tokenResult = $usuario ->createToken("login");        
        $token = $tokenResult->plainTextToken;
        // Responder
        return response()->json([
            "access_token" => $token,
            "token_type" => "Bearer",
            "usuario" => $usuario
        ]);

    }

    public function registro(Request $request){

        // Validar
        $request->validate([
            "name" => "required",
            "email" => "required | email | unique:users",
            "password" => "required",
            "c_password" => "required | same:password"
        ]);

        // registro
        $usuario = New User();
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->password = bcrypt($request->password);
        $usuario->save();

        // responder
        return response()->json(["mensaje" => "Usuario Registrado"], 201);

    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            "mensaje" => "Logged Out"
        ]);
    }

    public function perfil(Request $request)
    {
        return response()->json($request->user());
    }

}
