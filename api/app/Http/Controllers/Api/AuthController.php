<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\otp;
use App\Models\User;
use App\Service\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
     /**
     * Authenticate User
     * @param Request $request
     * @return User 
     */
    public function Auth2(Request $request)
    {
        try {
            //Validated
            $validateUser = Validator::make($request->all(), 
            [
                "oauth_id" =>"required",
                "provider" => 'required|in:google,facebook',
                "username" => "required",
                "email" => "required|email"
            ]);

            //Validation failed
            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            //Authentification
            $response = AuthService::Auth2($request);

            return $response;

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
    function send_code_phone_verify(Request $request)  {

      try {
        
        $validation = Validator::make($request->all(), 
        [
            "phone_number" => 'required',
        ]);

         //Validation failed
         if($validation->fails()){
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validation->errors()
            ], 401);
        }

        //Authentification
        $response = AuthService::send_code_phone_verification($request);

        return $response;

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
    function code_phone_verify(Request $request, $uuid)  {

        try {
            $validation = Validator::make($request->all(), 
            [
                "code" => 'required',
            ]);
            
            //Validation failed
            if($validation->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validation->errors()
            ], 401);
            }
           
            $response = AuthService::code_otp_verification($request, $uuid);
            return $response;
        
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
