<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\Api\RecipeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix('user')->group(function () {
    Route::post('/registrar', [UserController::class, 'registrarUsuario']);
    Route::post('/login', [UserController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('user')->group(function () {
        Route::get('/', function (Request $request) {
            return $request->user();
        });
        Route::post('/logout', [UserController::class, 'logout']);
    });
    
    Route::prefix('recetas')->group(function () {
        Route::post('/like', [LikeController::class, 'agregarLike']);
        Route::delete('/eliminar_like/{id}', [LikeController::class, 'delete']); 
        Route::get('/likes', [LikeController::class, 'listaLikes']); 
        Route::get('/random', [RecipeController::class, 'obtenerRecetasRandom']);
    });

});