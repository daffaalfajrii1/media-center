<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_home_shows_profil_dropdown_pages(): void
    {
        Page::create([
            'title' => 'Kata Sambutan',
            'slug' => 'kata-sambutan',
            'menu_group' => Page::GROUP_PROFIL,
            'content' => '<p>Isi halaman</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Kata Sambutan');
        $response->assertSee(route('public.profil.show', 'kata-sambutan'), false);
    }

    public function test_public_can_view_published_profil_page(): void
    {
        Page::create([
            'title' => 'Visi dan Misi',
            'slug' => 'visi-dan-misi',
            'menu_group' => Page::GROUP_PROFIL,
            'content' => '<p>Konten visi misi</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('public.profil.show', 'visi-dan-misi'));

        $response->assertOk();
        $response->assertSee('Visi dan Misi');
        $response->assertSee('Konten visi misi');
    }

    public function test_admin_edit_page_includes_rich_text_editor(): void
    {
        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage profil', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo(['view dashboard', 'manage profil']);

        $page = Page::create([
            'title' => 'Kata Sambutan',
            'slug' => 'kata-sambutan',
            'menu_group' => Page::GROUP_PROFIL,
            'content' => '<p>Test</p>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('admin.halaman.edit', $page));

        $response->assertOk();
        $response->assertSee('tinymce.min.js', false);
        $response->assertSee('id="page-content"', false);
    }

    public function test_admin_can_upload_editor_image(): void
    {
        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage profil', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo(['view dashboard', 'manage profil']);

        $file = \Illuminate\Http\UploadedFile::fake()->image('foto.jpg', 800, 600);

        $response = $this->actingAs($user)->postJson(route('admin.halaman.editor-upload'), [
            'file' => $file,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['url', 'location']);
    }

    public function test_admin_can_create_profil_page(): void
    {
        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage profil', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo(['view dashboard', 'manage profil']);

        $response = $this->actingAs($user)->post(route('admin.halaman.store'), [
            'title' => 'Struktur Organisasi',
            'menu_group' => Page::GROUP_PROFIL,
            'content' => '<p>Struktur organisasi kecamatan</p>',
            'sort_order' => 10,
            'is_published' => 1,
        ]);

        $response->assertRedirect(route('admin.halaman.index'));
        $this->assertDatabaseHas('pages', [
            'title' => 'Struktur Organisasi',
            'slug' => 'struktur-organisasi',
            'menu_group' => Page::GROUP_PROFIL,
        ]);
    }
}
