<?php

namespace App\Enums;

enum ProviderEnum: string
{
    case google = 'google';
    case facebook = 'facebook';

    public function isGoogle(): bool
    {
        return $this == ProviderEnum::google;
    }

    public function isFacebook(): bool
    {
        return $this == ProviderEnum::facebook;
    }
}