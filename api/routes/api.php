<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\PhoneNumberVerificationController;
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
    
    Route::post('/register/oauth2', [LoginController::class, "login"])->name("oauth2RegisterOrLogin");

    //Group authentification          Todo :: export it in others file
    Route::name('oauth2.')->group(function () {
       
        Route::post('/phone/verify', [PhoneNumberVerificationController::class, "sendVerificationCode"])
                ->name("sendCodePhoneVerification")
                ->middleware(['auth:sanctum', 'ability:limited']);

        Route::post('/phone/verify/{uuid}/check', [PhoneNumberVerificationController::class, "checkVerificationCode"])
                ->name("VerifyCodePhoneOtp")
                ->middleware(['auth:sanctum', 'ability:limited']);
    });  

  
    

    
    

});

