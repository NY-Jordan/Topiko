<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\OtpKindEnum;
use App\Http\Controllers\Controller;
use App\Http\Middleware\OnlyNotVerified;
use App\Http\Requests\PhoneNumberVerificationRequest;
use App\Http\Requests\SendPhoneNumberVerificationRequest;
use App\Http\Resources\OtpResource;
use App\Http\Resources\UserTokenResource;
use App\Models\Otp;
use App\Models\User;
use App\Service\OtpService;
use Illuminate\Support\Facades\Auth;

class PhoneNumberVerificationController extends Controller
{
    public function __construct(public OtpService $otpService)
    {
        $this->middleware(OnlyNotVerified::class);
    }

    public function sendVerificationCode(SendPhoneNumberVerificationRequest $request): OtpResource
    {
        /** @var User $user */
        $user = Auth::user();
        $otp = $this->otpService->createAndSend(
            $user,
            OtpKindEnum::PHONE_NUMBER_VALIDATION,
            $request->phone_number
        );
        return new OtpResource($otp);
    }

    public function checkVerificationCode(string $uuid, PhoneNumberVerificationRequest $request)
    {
        // retrouver l'otp
        $otp = Otp::findNotVerifiedByUuidAndKind(
            $uuid,
            OtpKindEnum::PHONE_NUMBER_VALIDATION
        );
        abort_if(!$this->otpService->check($otp, $request->code), 400, 'otp is not valid');
        /** @var User */
        $user = Auth::user();
        $user->markPhoneNumberAsVerified();
        $otp->setverified();
        return UserTokenResource::make($user);
    }
}