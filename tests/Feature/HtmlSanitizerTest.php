<?php

namespace Tests\Feature;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class HtmlSanitizerTest extends TestCase
{
    #[DataProvider('unsafeHtmlProvider')]
    public function test_it_strips_unsafe_html(string $input, string $expectedFragment): void
    {
        $cleaned = HtmlSanitizer::clean($input);

        $this->assertStringNotContainsString('<script', $cleaned);
        $this->assertStringNotContainsString('javascript:', $cleaned);
        $this->assertStringNotContainsString('onerror=', $cleaned);
        $this->assertStringContainsString($expectedFragment, $cleaned);
    }

    public static function unsafeHtmlProvider(): array
    {
        return [
            'script tag' => ['<p>Hello</p><script>alert(1)</script>', 'Hello'],
            'javascript href' => ['<a href="javascript:alert(1)">Click</a>', '<a href="#">Click</a>'],
            'inline handler' => ['<img src="x" onerror="alert(1)">', '<img src="x">'],
            'keeps safe markup' => ['<p><strong>Bold</strong></p>', '<strong>Bold</strong>'],
        ];
    }
}
