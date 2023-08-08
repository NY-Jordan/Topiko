<?php

namespace App\Service;

use App\Models\otp;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService {
    
    /**
     * Authentification with social media
     * @return token with capacity limited 
     */
    static function Auth2(Request $request)  {
        try {
            //verify if user already exist
            $user = User::where("email", $request->email)->first();
            $token = "";
            $data = [
                'username' => $request->username,
                'email' => $request->email,
                "google_oauth2_token" => $request->provider === "google" ? $request->oauth_id : null,
                "facebook_oauth2_token" => $request->provider === "facebook" ? $request->oauth_id : null,
                "updated_at" => now(),
                "created_at" => now(),
            ];
            if ($user) {
                $user->username = $request->username;
                $user->save();
                //if phone number has been verified generate a token with all acces
                if ($user->phone_number_verified) {
                    $token = $user->createToken("API TOKEN", ['phone:verification', 'user:all:request'])->plainTextToken;
                } else  
                    $token = $user->createToken("API TOKEN", ['phone:verification'])->plainTextToken;
   
            } else {
                $user = User::create($data);
               
                $token = $user->createToken("Auth2 register token", ['phone:verification'])->plainTextToken;
            }
   
            return response()->json([
               'status' => true,
               'message' => 'User Created Successfully',
               'token' => $token,
               'user' =>  [
                    'username' => $request->username,
                    'email' => $request->email,
                    "updated_at" => now(),
                    "created_at" => now(),
               ]
           ], 200);
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
    static function send_code_phone_verification(Request $request) {
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

            return response()->json([
                'status' => true,
                'message' => 'phone verification code sent Successfully',
                'otp' => [
                    "uuid"  => $uuid,
                    "expired_at" => $experited_at
                ],
            ], 200);

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
    static function code_otp_verification(Request $request, $uuid)  {
        try {
            //get opt by uuid
            $otp = otp::where('uuid', $uuid)->first();
            if ($otp) {
                //check if is already verfied
                if (!$otp->verified) {
                    if ($otp->reason === "PHONE_NUMBER_VALIDATION" && $otp->expired_at > now() ) {
                        if ($otp->code === $request->code) {
                            //create api token with all access    
                            $token  = User::find(Auth::user()->id)->createToken('API TOKEN', ['phone:verification', 'user:all:request'])->plainTextToken;
                            //change status of otp
                            $otp->verified = true;
                            $otp->save();

                            //change status phone number verified
                            $user  = User::find(Auth::user()->id);
                            $user->phone_number_verified = true;
                            $user->save();

                            return response()->json([
                                'status' => false,
                                'message'  => "Phone verification successfully",
                                'token' => $token,
                                'user' => [
                                    'username' => Auth::user()->username,
                                    'email' => Auth::user()->email,
                                ]
                            ], 200);
                        } else {
                            return response()->json([
                                'status' => false,
                                'message'  => "Incorrect code ",
                            ], 405);
                        }
                           
                    }else {
                        return response()->json([
                            'status' => false,
                            'message'  => "otp expired or reason error",
                        ], 400);
                    }
                } else     {
                    return response()->json([
                        'status' => false,
                        'message'  => "otp already verify",
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message'  => "otp not found, uuid is incorrect",
                ], 404);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message'  => $th->getMessage(),
            ], 500);
        }
    }
}