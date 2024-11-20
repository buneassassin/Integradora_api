<?php

use App\Http\Controllers\autenticadorController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\tinacoController;
use App\Http\Controllers\notificationController;
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
// Link para el registro
Route::post('v1/register', [autenticadorController::class, 'register']);
Route::get('v1/activate/{user}', [autenticadorController::class, 'activate'])->name('activate');//->middleware('signed');
Route::post('v1/login', [autenticadorController::class, 'login']);
Route::post('v1/update', [autenticadorController::class, 'update'])->middleware('auth:sanctum');
Route::post('v1/updatePassword', [autenticadorController::class, 'updatePassword'])->middleware('auth:sanctum');
Route::post('v1/logout', [autenticadorController::class, 'logout'])->middleware('auth:sanctum');
Route::get('v1/me', [autenticadorController::class, 'me'])->middleware('auth:sanctum');
// Link para el cambio de contraseña
Route::post('v1/reset-password', [autenticadorController::class, 'recuperarPassword']);
Route::get('reset-password/{user}', [autenticadorController::class, 'showResetForm'])->name('reset-password');
Route::post('reset-password/{user}', [autenticadorController::class, 'resetPassword']);
// Link para la imagen
Route::post('v1/imagen', [ImagenController::class, 'store'])->middleware('auth:sanctum');
Route::get('v1/imagen', [ImagenController::class, 'ver'])->middleware('auth:sanctum');

// Link para el tinaco
Route::post('v1/tinaco', [TinacoController::class, 'agregartinaco'])->middleware('auth:sanctum');
Route::get('v1/tinaco', [TinacoController::class, 'listartinacos'])->middleware('auth:sanctum');
Route::delete('v1/tinaco/{id}', [TinacoController::class, 'eliminartinaco'])->middleware('auth:sanctum');
Route::get('v1/tinaco/{id}', [TinacoController::class, 'gettinaco'])->middleware('auth:sanctum');
Route::put('v1/tinaco/{id}', [TinacoController::class, 'actualizartinaco'])->middleware('auth:sanctum');
// Link para las notificaciones
Route::get('v1/notifications', [notificationController::class, 'index'])->middleware('auth:sanctum');
Route::put('v1/notifications/{id}', [notificationController::class, 'markAsRead'])->middleware('auth:sanctum');
Route::delete('v1/notifications/{id}', [notificationController::class, 'destroy'])->middleware('auth:sanctum');