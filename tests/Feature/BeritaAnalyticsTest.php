<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\BeritaDailyView;
use App\Models\User;
use App\Models\WebsiteSetting;
use App\Services\BeritaAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BeritaAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage berita', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo(['view dashboard', 'manage berita']);

        return $user;
    }

    public function test_berita_view_records_daily_analytics(): void
    {
        $author = User::factory()->create();

        $berita = Berita::create([
            'user_id' => $author->id,
            'title' => 'Berita Analitik',
            'slug' => 'berita-analitik',
            'content' => '<p>Konten.</p>',
            'status' => Berita::STATUS_PUBLISHED,
            'published_at' => now(),
            'views_count' => 0,
        ]);

        $this->get(route('public.berita.show', $berita->slug))->assertOk();

        $berita->refresh();
        $this->assertSame(1, $berita->views_count);

        $this->assertDatabaseHas('berita_daily_views', [
            'berita_id' => $berita->id,
            'views_count' => 1,
        ]);
    }

    public function test_analytics_dashboard_returns_chart_data(): void
    {
        $author = User::factory()->create();

        $berita = Berita::create([
            'user_id' => $author->id,
            'title' => 'Berita Chart',
            'slug' => 'berita-chart',
            'content' => '<p>Konten.</p>',
            'status' => Berita::STATUS_PUBLISHED,
            'published_at' => now(),
            'views_count' => 5,
        ]);

        BeritaDailyView::record($berita->id, now()->toDateString());
        BeritaDailyView::record($berita->id, now()->toDateString());

        $dashboard = app(BeritaAnalyticsService::class)->dashboard(7);

        $this->assertSame(7, $dashboard['range']);
        $this->assertCount(7, $dashboard['labels']);
        $this->assertCount(7, $dashboard['views']);
        $this->assertGreaterThanOrEqual(2, $dashboard['views_period']);
        $this->assertTrue($dashboard['top_beritas']->isNotEmpty());
    }

    public function test_public_layout_includes_google_analytics_when_enabled(): void
    {
        WebsiteSetting::query()->create(array_merge(WebsiteSetting::defaults(), [
            'google_analytics_enabled' => true,
            'google_analytics_measurement_id' => 'G-TEST12345',
        ]));
        WebsiteSetting::clearCache();

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('googletagmanager.com/gtag/js?id=G-TEST12345', false);
        $response->assertSee("gtag('config', 'G-TEST12345'", false);
    }

    public function test_admin_statistik_page_shows_analytics_dashboard(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->get(route('admin.berita.statistik', ['range' => 30]));

        $response->assertOk();
        $response->assertSee('Analitik Pembacaan Berita');
        $response->assertSee('Tren Pembacaan');
        $response->assertSee('Google Analytics');
        $response->assertSee('beritaViewsChart', false);
    }
}
