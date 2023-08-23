<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\ProviderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'google_oauth2_token',
        'facebook_oauth2_token',
        'phone_number',
        'phone_number_verified',
        'email_verified_at',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function findByEmail(string $email): ?self
    {
        return User::where("email", $email)->first();
    }

    public function setOauthId(ProviderEnum $provider, string $id)
    {
        if ($provider->isGoogle()) {
            $this->google_oauth2_token = $id;
        }
        if ($provider->isFacebook()) {
            $this->facebook_oauth2_token = $id;
        }
    }

    public function getToken(): NewAccessToken
    {
        if ($this->phone_number_verified) {
            return $this->createToken("API TOKEN", ['*']);
        }
        return $this->createToken("API TOKEN", ['limited']);
    }

    public function getVerifiedAttribute(): bool
    {
        return $this->phone_number_verified_at != null;
    }

    public function markPhoneNumberAsVerified(): void
    {
        $this->phone_number_verified_at = now();
    }
}