<?php

namespace App\Service;

use App\Enums\ProviderEnum;
use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

class AuthService
{
    public function __construct(
        private OtpService $otpService
    ) {

    }

    /**
     * Authentification with social media
     * @return NewAccessToken with capacity limited 
     */
    public function login(
        string $username,
        string $email,
        string $oauthId,
        ProviderEnum $provider,
    ): NewAccessToken {
        //verify if user already exist
        $user = User::findByEmail($email);
        if (!$user) {
            $user = $this->createUser($username, $email, $oauthId, $provider);
        }
        return $user->getToken();
    }

    protected function createUser(
        string $username,
        string $email,
        string $oauthId,
        ProviderEnum $provider
    ): User {
        $user = new User;
        $user->fill(compact('username', 'email'));
        $user->setOauthId($provider, $oauthId);
        $user->save();
        return $user;
    }
}