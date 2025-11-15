<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private function reglasUsuario($id = null)
    {
        return [
            'nombre' => 'required|string|max:200',
            'apellido' => 'required|string',
            'correo' => 'required|email|unique:users,correo,' . $id,
            'password' => 'required|string|min:8',
        ];
    }

    private function usuarioCorreo($correo)
    {
        return User::where('correo', $correo)->first();
    }

    private function usuarioId($id)
    {
        return User::find($id);
    }

    // Función privada para validar el token y devolver el usuario
    private function validateToken(Request $request)
    {
        //Obetenemos el token del header de Authorization (Beaner token)
        $token = $request->bearerToken();
        
        //Si no hay token, se retorna null, el usuario no tiene sesión
        if(!$token){
            return null;
        }

        //Buscamos el token en la tabla de PersonalAccessToken
        $accessToken = PersonalAccessToken::findToken($token);

        //Si no existe en la BD se retorna null
        if(!$accessToken){
            return null;
        }

        //Verificamos que el token pertenezca al modelo Usuario
        if($accessToken->tokenable_type !== User::class){
            return null;
        }

        //Retornamos el token con el usuario
        return $accessToken->tokenable;
    }

    // registrarUsuario
    public function registrarUsuario(Request $request)
    {
        //Validamos todos los campos que ya definimos
        $validator = Validator::make($request->all(), $this->reglasUsuario());
        
        //Si la validación falla, devolver un error
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $datos = $request->all();
        $datos['password'] = bcrypt($datos['password']);
        
        $usuario = User::create($datos);
        
        return response()->json([
            'mensaje' => 'Usuario registrado correctamente',
            'usuario' => $usuario
        ], 201);
    }

    // iniciarSesion
    public function login(Request $request)
    {
        // No valido token aquí porque aqui lo obtengo
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        //Consulta con el metodo privado
        $usuario = $this->usuarioCorreo($request->correo);
        
        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json(['mensaje' => 'Credenciales inválidas'], 401);
        }
        
        // Aquí teiene que generar un token real :)
        $token = $usuario->createToken('Token')->plainTextToken;

        return response()->json([
            'mensaje' => 'Inicio de sesión exitoso',
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->nombre,
                'apellido' => $usuario->apellido,
                'correo' => $usuario->correo,
                'token' => $token,
                ]
            ], 200);
    }

    public function logout(Request $request)
    {
        $token = $request->input('token');
        $accessToken = PersonalAccessToken::findToken($token);

        if ($accessToken) {
            $accessToken->delete();
        }

        return response()->json(['message' => 'Sesión cerrada con éxito']);
    }
}
