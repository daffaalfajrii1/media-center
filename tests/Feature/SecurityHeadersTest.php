<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;
    public function test_public_pages_include_security_headers(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_homepage_includes_seo_meta_tags(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('meta name="description"', false);
        $response->assertSee('rel="canonical"', false);
        $response->assertSee('property="og:title"', false);
        $response->assertSee('name="twitter:card"', false);
    }
}
