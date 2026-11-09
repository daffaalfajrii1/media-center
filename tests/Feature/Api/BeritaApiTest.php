<?php

namespace Tests\Feature\Api;

use App\Models\Berita;
use App\Models\BeritaCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BeritaApiTest extends TestCase
{
    use RefreshDatabase;

    private function publisher(): User
    {
        return User::factory()->create();
    }

    private function publishedBerita(User $user, string $title, ?string $slug = null): Berita
    {
        return Berita::create([
            'user_id' => $user->id,
            'title' => $title,
            'slug' => $slug ?: \Illuminate\Support\Str::slug($title),
            'excerpt' => 'Ringkasan '.$title,
            'content' => '<p>Konten '.$title.'</p>',
            'status' => Berita::STATUS_PUBLISHED,
            'published_at' => now(),
            'thumbnail' => 'beritas/thumbnails/contoh.jpg',
        ]);
    }

    public function test_berita_terbaru_requires_sanctum_token(): void
    {
        $response = $this->getJson('/api/v1/berita/terbaru');

        $response->assertUnauthorized();
    }

    public function test_berita_terbaru_returns_latest_published_items(): void
    {
        $user = $this->publisher();
        $this->publishedBerita($user, 'Berita Lama', 'berita-lama')->update([
            'published_at' => now()->subDays(2),
        ]);
        $this->publishedBerita($user, 'Berita Baru', 'berita-baru')->update([
            'published_at' => now()->subHour(),
        ]);
        Berita::create([
            'user_id' => $user->id,
            'title' => 'Draft Rahasia',
            'slug' => 'draft-rahasia',
            'content' => '<p>Draft</p>',
            'status' => Berita::STATUS_DRAFT,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/berita/terbaru?limit=4');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('count', 2)
            ->assertJsonPath('data.0.title', 'Berita Baru')
            ->assertJsonPath('data.0.slug', 'berita-baru')
            ->assertJsonStructure([
                'success',
                'source',
                'count',
                'data' => [
                    [
                        'id',
                        'title',
                        'slug',
                        'excerpt',
                        'featured_image',
                        'published_at_formatted',
                        'url',
                        'read_more_url',
                    ],
                ],
            ]);

        $titles = collect($response->json('data'))->pluck('title');
        $this->assertFalse($titles->contains('Draft Rahasia'));
    }

    public function test_api_media_urls_use_app_url_not_request_host(): void
    {
        config(['app.url' => 'https://mediacenter.rejanglebongkab.go.id']);

        $user = $this->publisher();
        $this->publishedBerita($user, 'Berita Foto Publik', 'berita-foto-publik');

        Sanctum::actingAs($user);

        $response = $this->getJson('http://10.90.120.9/api/v1/berita/terbaru?limit=1', [
            'Host' => 'mediacenter.rejanglebongkab.go.id',
        ]);

        $response->assertOk();
        $image = $response->json('data.0.featured_image');
        $url = $response->json('data.0.url');

        $this->assertNotNull($image);
        $this->assertStringStartsWith('https://mediacenter.rejanglebongkab.go.id/', $image);
        $this->assertStringNotContainsString('10.90.120.9', (string) $image);
        $this->assertStringNotContainsString('//storage', (string) $image);
        $this->assertSame('https://mediacenter.rejanglebongkab.go.id/berita/berita-foto-publik', $url);
    }

    public function test_berita_index_is_paginated(): void
    {
        $user = $this->publisher();
        foreach (range(1, 3) as $i) {
            $this->publishedBerita($user, "Berita {$i}", "berita-{$i}");
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/berita?per_page=2');

        $response->assertOk()
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonCount(2, 'data');
    }

    public function test_berita_show_returns_detail_by_slug(): void
    {
        $user = $this->publisher();
        $category = BeritaCategory::create([
            'name' => 'Pemerintahan',
            'slug' => 'pemerintahan',
            'is_active' => true,
        ]);

        $berita = $this->publishedBerita($user, 'Detail Berita API', 'detail-berita-api');
        $berita->update(['berita_category_id' => $category->id]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/berita/detail-berita-api');

        $response->assertOk()
            ->assertJsonPath('data.title', 'Detail Berita API')
            ->assertJsonPath('data.slug', 'detail-berita-api')
            ->assertJsonPath('data.category.slug', 'pemerintahan')
            ->assertJsonStructure(['data' => ['content', 'likes_count']]);
    }

    public function test_berita_show_returns_404_for_unknown_slug(): void
    {
        Sanctum::actingAs($this->publisher());

        $this->getJson('/api/v1/berita/tidak-ada')
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }
}
