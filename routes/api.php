<?php

use App\Http\Controllers\autenticadorController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\TinacoController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TemperaturaController;
use App\Http\Controllers\phController;
use App\Http\Controllers\turbidezController;
use App\Http\Controllers\TDSController;
use App\Http\Controllers\ultrasonicoController;
use App\Http\Controllers\AdafruitController;

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
Route::get('v1/activate/{user}', [autenticadorController::class, 'activate'])->name('activate'); //->middleware('signed');
Route::post('v1/login', [autenticadorController::class, 'login']); //->middleware('inactive.block');
Route::post('v1/update', [autenticadorController::class, 'update'])->middleware('auth:sanctum');
Route::post('v1/updatePassword', [autenticadorController::class, 'updatePassword'])->middleware('auth:sanctum');
Route::post('v1/logout', [autenticadorController::class, 'logout'])->middleware('auth:sanctum');
Route::get('v1/me', [autenticadorController::class, 'me'])->middleware(['auth:sanctum','inactive.block']);
// Link para el cambio de contraseña
Route::post('v1/reset-password', [autenticadorController::class, 'recuperarPassword']);
Route::get('reset-password/{user}', [autenticadorController::class, 'showResetForm'])->name('reset-password');
Route::post('reset-password/{user}', [autenticadorController::class, 'resetPassword']);
// 'user.admin', 'inactive.block','active.only'
Route::middleware(['auth:sanctum'])->group(function () {
    // Link para la imagen
    Route::post('v1/imagen', [ImagenController::class, 'store']);
    Route::get('v1/imagen', [ImagenController::class, 'ver']);
    // Link para el tinaco
    Route::post('v1/tinaco', [TinacoController::class, 'agregartinaco']);
    Route::get('v1/tinaco', [TinacoController::class, 'listartinacos']);
    Route::delete('v1/tinaco/{id}', [TinacoController::class, 'eliminartinaco']);
    Route::get('v1/tinaco/{id}', [TinacoController::class, 'gettinaco']);
    Route::put('v1/tinaco/{id}', [TinacoController::class, 'actualizartinaco']);
    // Link para las notificaciones
    Route::get('v1/notifications', [notificationController::class, 'index']);
    Route::put('v1/notifications/{id}', [notificationController::class, 'markAsRead']);
    Route::delete('v1/notifications/{id}', [notificationController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'admin.only'])->group(function () {
    Route::get('v1/admin-action', [AdminController::class, 'performAction']);
    Route::get('v1/usuariosConTinacos', [AdminController::class, 'obtenerUsuariosConTinacos']);
    Route::post('v1/desactivarUsuario', [AdminController::class, 'desactivarUsuario']);
    Route::post('v1/cambiarRol', [AdminController::class, 'cambiarRol']);
    Route::get('v1/getUserStatistics', [AdminController::class, 'getUserStatistics']);
    Route::get('v1/obtenerRol', [AdminController::class, 'obtenerRol']);
    Route::post('v1/EnviarNotificacionesGeneral', [notificationController::class, 'EnviarNotificacionesGeneral']);
    Route::get('v1/gettype', [notificationController::class, 'gettype']);

});
Route::get('v1/ada/{feed}', [AdafruitController::class, 'getFeedData']);

Route::post('v1/temperatura', [TemperaturaController::class, 'obtenertemp']);

Route::post('v1/ph', [phController::class, 'obtenerph']);
Route::post('v1/turbidez', [turbidezController::class, 'obtenerturbidez']);
Route::post('v1/tds', [TDSController::class, 'obtenerturbidez']);
Route::post('v1/ultrasonico', [ultrasonicoController::class, 'obtenerturbidez']);