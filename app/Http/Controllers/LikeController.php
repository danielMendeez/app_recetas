<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LikeController extends Controller
{
    public function agregarLike(Request $request)
    {
        $userId = Auth::id();

        //Validamos todos los campos que ya definimos
        $validator = Validator::make($request->all(), [
            'receta_id' => 'required|integer',
            'titulo_receta' => 'required|string',
            'imagen_url' => 'required|string',
        ]);
        
        //Si la validación falla, devolver un error
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $datos = $request->only(['receta_id', 'titulo_receta', 'imagen_url']);
        $datos['user_id'] = $userId;

        $like = Like::create($datos);
        
        return response()->json([
            'mensaje' => 'Like registrado correctamente',
            'usuario' => $like
        ], 201);
    }

    public function delete(Request $request, $id)
    {
        $usuario = $request->user();

        $like = Like::find($id);

        if(!$like){
            return response()->json(['message' => 'Like no encontrado'], 404);
        }

        if ($like->user_id !== $usuario->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $like->delete();
        return response()->json(['message' => 'Like eliminado con éxito'], 200);
    }

    public function listaLikes(Request $request)
    {
        $usuario = $request->user();

        $likes = $usuario->likes;

        return response()->json(['likes' => $likes], 200);
    }
}
