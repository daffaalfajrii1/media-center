<?php

namespace App\Support;

class SeoUrl
{
    public static function absolute(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $url)) {
            return $url;
        }

        return url($url);
    }
}
