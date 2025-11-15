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
            'number' => 10
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'No se pudo obtener las recetas'], $response->status());
        }

        $recipes = $response->json()['recipes'];

        // Formatear la respuesta para Flutter
        $formattedRecipes = array_map(function($recipe) {
            return [
                'id' => $recipe['id'],
                'title' => $recipe['title'],
                'image' => $recipe['image'],
                'readyInMinutes' => $recipe['readyInMinutes'],
                'servings' => $recipe['servings'],
                'vegetarian' => $recipe['vegetarian'],
                'vegan' => $recipe['vegan'],
                'glutenFree' => $recipe['glutenFree'],
                'dairyFree' => $recipe['dairyFree'],
                'pricePerServing' => $recipe['pricePerServing'],
                'summary' => $recipe['summary'],
                'dishTypes' => $recipe['dishTypes'] ?? [],
                'diets' => $recipe['diets'] ?? [],
                'extendedIngredients' => $recipe['extendedIngredients'] ?? [],
                'analyzedInstructions' => $recipe['analyzedInstructions'] ?? [],
                'instructions' => $recipe['instructions'] ?? '',
            ];
        }, $recipes);

        return response()->json($formattedRecipes);
    }
}
