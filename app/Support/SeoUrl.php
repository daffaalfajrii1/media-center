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

    /**
     * Paksa URL absolut memakai APP_URL publik (bukan IP internal request).
     */
    public static function publicAbsolute(?string $pathOrUrl): ?string
    {
        if (blank($pathOrUrl)) {
            return null;
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        if ($appUrl === '') {
            return self::absolute($pathOrUrl);
        }

        if (preg_match('/^https?:\/\//i', $pathOrUrl)) {
            $path = parse_url($pathOrUrl, PHP_URL_PATH) ?: '/';
            $query = parse_url($pathOrUrl, PHP_URL_QUERY);

            return $appUrl.$path.($query ? '?'.$query : '');
        }

        return $appUrl.'/'.ltrim($pathOrUrl, '/');
    }
}

