<?php

namespace App\Service;

use App\Enums\OtpEnums;
use App\Models\otp;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OtpService {

    public function verifyOtpExpiration(otp $otp) {

        if ($otp->reason === OtpEnums::PHONE_NUMBER_VALIDATION->name && !$otp->expiried ) {
            return true;
        } else return false;
    }
   
    function checkIfOtpIsAlreadyVerify(otp $otp)  {

        if (!$otp->verified) {
            return true;
        } else return false;
    }

    public function verifyOtpCode(otp $otp, $code)  {
        //check if otp is not verified
        if ($otp->code === $code) {
            return true;
        } else return false;
       
    }

}