<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecipeController extends Controller
{
    public function obtenerRecetasRandom(Request $request)
    {
        $apiKey = env('SPOONACULAR_API_KEY');

        if (!$apiKey) {
            return response()->json(['error' => 'Clave de API no configurada'], 500);
        }

        $response = Http::get('https://api.spoonacular.com/recipes/random', [
            'apiKey' => $apiKey,
            'number' => 5
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'No se pudo obtener las recetas'], $response->status());
        }

        return $response->json()['recipes']; 
    }
}
