<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_404_page_is_rendered(): void
    {
        $response = $this->get('/halaman-yang-tidak-ada-'.uniqid());

        $response->assertNotFound();
        $response->assertSee('Halaman Tidak Ditemukan', false);
        $response->assertSee('Maaf, halaman yang Anda cari tidak tersedia', false);
        $response->assertSee('Kembali ke Beranda', false);
        $response->assertSee(route('home'), false);
        $response->assertSee('noindex, follow', false);
    }
}
