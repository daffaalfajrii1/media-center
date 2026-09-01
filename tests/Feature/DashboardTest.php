<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\BeritaCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'view dashboard', 'manage berita', 'manage agenda', 'manage layanan',
            'manage pengaturan', 'manage users', 'manage roles',
        ] as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
    }

    private function superAdmin(): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole('super-admin');
        Permission::all()->each(fn ($p) => $user->givePermissionTo($p));

        return $user;
    }

    public function test_super_admin_sees_integrated_dashboard(): void
    {
        $user = $this->superAdmin();
        $category = BeritaCategory::create(['name' => 'Umum', 'slug' => 'umum', 'is_active' => true]);

        Berita::create([
            'berita_category_id' => $category->id,
            'user_id' => $user->id,
            'title' => 'Berita Terbaru',
            'slug' => 'berita-terbaru',
            'status' => Berita::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        Agenda::create([
            'user_id' => $user->id,
            'title' => 'Rapat Koordinasi',
            'slug' => 'rapat-koordinasi',
            'event_type' => Agenda::TYPE_RAPAT,
            'event_date' => now()->addDays(3)->toDateString(),
            'start_time' => '09:00:00',
            'status' => Agenda::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Ringkasan Modul Konten');
        $response->assertSee('Aktivitas Terbaru');
        $response->assertSee('Agenda Mendatang');
        $response->assertSee('Status Pengajuan');
        $response->assertSee('Pengajuan Baru');
    }

    public function test_user_without_dashboard_permission_cannot_access(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }
}
