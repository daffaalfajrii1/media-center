<?php

namespace App\Support;

class Youtube
{
    public static function extractVideoId(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/',
            '/^([a-zA-Z0-9_-]{11})$/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public static function thumbnailUrl(?string $videoId, string $quality = 'hqdefault'): ?string
    {
        if (! $videoId) {
            return null;
        }

        return "https://img.youtube.com/vi/{$videoId}/{$quality}.jpg";
    }

    public static function embedUrl(?string $videoId): ?string
    {
        if (! $videoId) {
            return null;
        }

        return "https://www.youtube.com/embed/{$videoId}";
    }
}
