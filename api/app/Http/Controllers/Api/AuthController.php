<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OAuth2Request;
use App\Http\Requests\SendPhoneCodeRequest;
use App\Http\Requests\VerifyCodePhoneOtpRequest;
use App\Models\otp;
use App\Models\User;
use App\Service\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct (
        private AuthService $authService
    ){

    }
     /**
     * Authenticate User
     * @param Request $request
     * @return User 
     */
    public function oauth2RegisterOrLogin(OAuth2Request $request)
    {
        try {
            //Authentification
            $response = $this->authService->Oauth2($request);

            return response()->json($response);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * send code for verify phone number
     * @param Request $request
     * @return otp 
     */
    public function sendCodePhoneVerification(SendPhoneCodeRequest $request)  {

      try {
        
        //Authentification
        $response = $this->authService->sendCodePhoneVerification($request);

        return response()->json($response);

      } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
      }


    }

      /**
     * Verify the code otp of phone number
     * @param Request $request
     * 
     */
    public function VerifyCodePhoneOtp(VerifyCodePhoneOtpRequest $request, $uuid)  {

        try {
           

            $response = $this->authService->codeOtpVerification($request, $uuid);

            return response()->json($response);
        
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
