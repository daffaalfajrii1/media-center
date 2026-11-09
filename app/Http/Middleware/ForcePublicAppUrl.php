<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pastikan asset()/route() di API selalu memakai APP_URL publik,
 * meskipun request masuk via IP internal (10.90.120.9) dari WordPress.
 */
class ForcePublicAppUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $root = rtrim((string) config('app.url'), '/');

        if ($root !== '') {
            URL::forceRootUrl($root);

            $scheme = parse_url($root, PHP_URL_SCHEME);
            if (is_string($scheme) && $scheme !== '') {
                URL::forceScheme($scheme);
            }
        }

        return $next($request);
    }
}
