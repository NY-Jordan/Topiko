<?php

namespace App\Models;

use App\Enums\OtpKindEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'code',
        'uuid',
        'expired_at',
        'reason',
        'phone_number',
        'verified',
    ];

    protected $casts = [
        'expires_at' => "datetime",
    ];

    public function getExpiredAttribute(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return now()->isAfter($this->expires_at);
    }

    public static function findNotVerifiedByUuidAndKind(string $uuid, OtpKindEnum $kind): ?self
    {
        return self::where('verified_at', '=', null)
            ->where('uuid', '=', $uuid)
            ->where('kind', '=', $kind->value)
            ->first();
    }
}