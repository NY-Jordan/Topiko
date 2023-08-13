<?php

namespace App\Service;

use App\Http\Requests\VerifyCodePhoneOtpRequest;
use App\Models\otp;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService {

    public function __construct(
        private OtpService $otpService
    )
    {
        
    }
    
    /**
     * Authentification with social media
     * @return token with capacity limited 
     */
    public function Oauth2( $request)  {
        try {
            //verify if user already exist
            $user = User::where("email", $request->email)->first();
            $token = "";
            if ($user) {
                $user->username = $request->username;
                $user->save();
                //if phone number has been verified generate a token with all acces
                if ($user->phone_number_verified) {
                    $token = $user->createToken("API TOKEN", ['phone:verification', 'user:all:request'])->plainTextToken;
                } else  
                    $token = $user->createToken("API TOKEN", ['phone:verification'])->plainTextToken;
   
            } else {
                $user = User::create([
                    'username' => $request->username,
                    'email' => $request->email,
                    "google_oauth2_token" => $request->provider === "google" ? $request->oauth_id : null,
                    "facebook_oauth2_token" => $request->provider === "facebook" ? $request->oauth_id : null,
                    "updated_at" => now(),
                    "created_at" => now(),
                ]);
               
                $token = $user->createToken("Auth2 register token", ['phone:verification'])->plainTextToken;
            }
   
            return [
               'status' => true,
               'message' => 'User Created Successfully',
               'token' => $token,
               'user' =>  [
                    'username' => $request->username,
                    'email' => $request->email,
                    "updated_at" => now(),
                    "created_at" => now(),
               ]
           ];
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message'  => $th->getMessage(),
            ], 500);
        }
        
    }

    /**
     * send verify code for phone number
     * @return uuid  
     */
    public function sendCodePhoneVerification( $request) {
        try {
            //created uuid
            $uuid = rand(10, 1000000); 
            $experited_at = date('Y-m-d H:i:s', time() + 900);

            //TODO : integrate sms send api 

            //create user otp
            otp::create([
                'user_id' => Auth::user()->id,
                'code' => '123456',
                'phone_number' => $request->phone_number,
                'uuid' => $uuid,
                "expired_at" => $experited_at,
                'reason' => "PHONE_NUMBER_VALIDATION",
                'verified' => false,
                'created_at' => now(), 
                'updated_at' => now(),
            ]);

            return [
                'status' => true,
                'message' => 'phone verification code sent Successfully',
                'otp' => [
                    "uuid"  => $uuid,
                    "expired_at" => $experited_at
                ],
            ];

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message'  => $th->getMessage(),
            ], 500);
        }
    }

     /**
     * verify the code otp and generate a api token 
     * @return token with all capacity  
     */
    public function codeOtpVerification($request, $uuid)  {
            //get opt by uuid
            $otp = otp::where('uuid', $uuid)->first();
            //check if otp 
            if (!$otp) {
                return [
                    'status' => false,
                    'message'  => "otp not found, uuid is incorrect",
                ];
            } 

            //Check if otp is verified
            if (!$this->otpService->checkIfOtpIsAlreadyVerify($otp)) {
                return [
                    'status' => false,
                    'message'  => "otp already verify",
                ];
            }
            //check if otp experied
            if (!$this->otpService->verifyOtpExpiration($otp)) {
                return [
                    'status' => false,
                    'message'  => "otp expired or reason error",
                ];
            }

            //check otp code
            if ($this->otpService->verifyOtpCode($otp, $request->code)) {
                //create api token with all access    
                $token  = User::find(Auth::user()->id)->createToken('API TOKEN', ['phone:verification', 'user:all:request'])->plainTextToken;
                //change status of otp
                $otp->verified = true;
                $otp->save();

                //change status phone number verified
                $user  = User::find(Auth::user()->id);
                $user->phone_number_verified = true;
                $user->save();

                return [
                    'status' => false,
                    'message'  => "Phone verification successfully",
                    'token' => $token,
                    'user' => [
                        'username' => Auth::user()->username,
                        'email' => Auth::user()->email,
                    ]
                ];
            } else {
                return [
                    'status' => false,
                    'message'  => "Incorrect code ",
                ];
            }
               
    }
}