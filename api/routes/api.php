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
    
    Route::post('/register/oauth2', [AuthController::class, "oauth2RegisterOrLogin"])->name("oauth2RegisterOrLogin");

    //Group authentification          Todo :: export it in others file
    Route::name('oauth2.')->group(function () {
       
        Route::post('/phone/verify', [AuthController::class, "sendCodePhoneVerification"])
                ->name("sendCodePhoneVerification")
                ->middleware(['auth:sanctum', 'ability:phone:verification']);

        Route::post('/phone/verify/{uuid}/check', [AuthController::class, "VerifyCodePhoneOtp"])
                ->name("VerifyCodePhoneOtp")
                ->middleware(['auth:sanctum', 'ability:phone:verification']);
    });  

    Route::middleware(['auth:sanctum', 'abilities:phone:verification,user:all:request'])->group(function () {
        Route::get('/menu', [MenuController::class, "get_all"]);
    });
    

    
    

});

