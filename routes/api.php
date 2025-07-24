<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CrudControllerPrueba;

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

// Ruta de ejemplo para autenticación de usuario (opcional)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$controllerPrueba = CrudControllerPrueba::class;

Route::get('/prueba', [$controllerPrueba, 'get']);
Route::post('/prueba', [$controllerPrueba, 'save']);
Route::put('/prueba', [$controllerPrueba, 'putOne']);
// Route::delete('/prueba/{id}', [$controllerPrueba, 'deleteOne']);