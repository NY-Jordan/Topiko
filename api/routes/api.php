<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
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
Route::prefix('api')->group(function () {
    
    //Group authentification          Todo :: export it in others file
    Route::name('oauth2.')->group(function () {
        Route::post('/register/auth2', [AuthController::class, "Auth2"])->name("auth2");
        Route::post('/phone/verify', [AuthController::class, "send_code_phone_verify"])
                ->name("send_code_phone_verify")
                ->middleware(['auth:sanctum', 'ability:phone:verification']);

        Route::post('/phone/verify/{uuid}/check', [AuthController::class, "code_phone_verify"])
                ->name("phone_verify")
                ->middleware(['auth:sanctum', 'ability:phone:verification']);
    });  

    Route::middleware(['auth:sanctum', 'abilities:phone:verification,user:all:request'])->group(function () {
        Route::get('/menu', [MenuController::class, "get_all"]);
    });
    

    
    

});

