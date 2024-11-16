<?php

use App\Http\Controllers\autenticadorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('v1/register', [autenticadorController::class, 'register']);
Route::get('v1/activate/{user}', [autenticadorController::class, 'activate'])->name('activate');//->middleware('signed');
Route::post('v1/login', [autenticadorController::class, 'login']);
Route::post('v1/logout', [autenticadorController::class, 'logout'])->middleware('auth:sanctum');
Route::get('v1/me', [autenticadorController::class, 'me'])->middleware('auth:sanctum');
