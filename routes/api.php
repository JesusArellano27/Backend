<?php

use App\Http\Controllers\InfoBD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('login', [InfoBD::class, 'login']);

Route::post('guardarnuevasala', [InfoBD::class, 'GuardarNuevaSala']);

Route::get('versalas', [InfoBD::class, 'VerSalas']);