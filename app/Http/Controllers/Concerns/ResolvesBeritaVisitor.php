<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;

trait ResolvesBeritaVisitor
{
    protected function visitorToken(Request $request): string
    {
        $token = $request->cookie('berita_visitor_token')
            ?? $request->session()->get('berita_visitor_token');

        if (is_string($token) && strlen($token) >= 32) {
            return $token;
        }

        return (string) Str::uuid();
    }

    protected function rememberVisitorToken(Request $request, string $token): void
    {
        $request->session()->put('berita_visitor_token', $token);
    }

    protected function visitorCookie(string $token): Cookie
    {
        return cookie(
            'berita_visitor_token',
            $token,
            60 * 24 * 365 * 2,
            '/',
            null,
            false,
            false,
            false,
            'Lax'
        );
    }
}
