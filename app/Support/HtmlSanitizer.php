<?php

namespace App\Support;

class HtmlSanitizer
{
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><a><img><table><thead><tbody><tr><th><td><figure><figcaption><hr><sub><sup><span><div>';

    public static function clean(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $html = strip_tags($html, self::ALLOWED_TAGS);
        $html = preg_replace('/\s+on\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/iu', '', $html) ?? $html;
        $html = preg_replace('/(href|src|xlink:href)\s*=\s*(["\'])\s*javascript:[^"\']*\2/iu', '$1=$2#$2', $html) ?? $html;
        $html = preg_replace('/(href|src|xlink:href)\s*=\s*(["\'])\s*data:(?!image\/)[^"\']*\2/iu', '$1=$2#$2', $html) ?? $html;

        return $html;
    }
}
