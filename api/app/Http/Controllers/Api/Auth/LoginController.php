<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\ProviderEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SendPhoneCodeRequest;
use App\Http\Requests\VerifyCodePhoneOtpRequest;
use App\Http\Resources\UserTokenResource;
use App\Models\otp;
use App\Service\AuthService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function __construct (
        private AuthService $authService
    ){

    }
     /**
     * Authenticate User
     * @param Request $request
     */
    public function login(LoginRequest $request)
    {
        //Authentification
        $user = $this->authService->login(
            $request->username,
            $request->email,
            $request->oauth_id,
            ProviderEnum::from($request->provider),
        );
        return UserTokenResource::make($user);
    }
}
