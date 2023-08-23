<?php

namespace App\Service;

use App\Enums\OtpEnums;
use App\Enums\OtpKindEnum;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class OtpService
{
    public function create(User $user, OtpKindEnum $kind, ?string $phoneNumber = null): Otp
    {
        if (!$phoneNumber && !$user->phone_number) {
            throw new \InvalidArgumentException("A phone number is required");
        }
        return Otp::create([
            'user_id' => $user->id,
            'code' => $this->generateOtpCode(),
            'phone_number' => $phoneNumber ?? $user->phone_number,
            'uuid' => Uuid::uuid4(),
            "expired_at" => now()->addMinutes(15),
            'kind' => $kind->value,
            'verified' => false,
        ]);
    }
    public function send(Otp $otp): Otp
    {
        return $otp;
    }

    public function createAndSend(User $user, OtpKindEnum $kind, ?string $phoneNumber = null): Otp
    {
        return $this->send(
            $this->create(
                $user,
                $kind,
                $phoneNumber
            )
        );
    }
    public function  check(Otp $otp, string $code): bool
    {
        return $otp->code === $code && !$otp->expired;

    }

    protected function generateOtpCode(int $length = 5): string
    {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }

}