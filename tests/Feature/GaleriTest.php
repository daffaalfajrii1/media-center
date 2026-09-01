<?php

namespace Tests\Feature;

use App\Models\Galeri;
use App\Models\GaleriItem;
use App\Models\User;
use App\Support\Youtube;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class GaleriTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage galeri', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo(['view dashboard', 'manage galeri']);

        return $user;
    }

    public function test_admin_can_view_galeri_index(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->get(route('admin.galeri.index'));

        $response->assertOk();
        $response->assertSee('Galeri Foto & Video');
    }

    public function test_admin_can_create_galeri_album(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->post(route('admin.galeri.store'), [
            'title' => 'Kegiatan 17 Agustus',
            'description' => 'Lomba antar desa',
            'event_date' => now()->format('Y-m-d'),
            'status' => Galeri::STATUS_PUBLISHED,
            'is_featured' => 1,
        ]);

        $galeri = Galeri::where('title', 'Kegiatan 17 Agustus')->first();
        $response->assertRedirect(route('admin.galeri.edit', $galeri));
        $this->assertDatabaseHas('galeris', [
            'title' => 'Kegiatan 17 Agustus',
            'slug' => 'kegiatan-17-agustus',
            'status' => Galeri::STATUS_PUBLISHED,
            'user_id' => $user->id,
        ]);
    }

    public function test_admin_can_upload_multiple_photos_to_album(): void
    {
        Storage::fake('public');
        $user = $this->adminUser();

        $galeri = Galeri::create([
            'user_id' => $user->id,
            'title' => 'Album Foto',
            'slug' => 'album-foto',
            'status' => Galeri::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($user)->post(route('admin.galeri.foto.store', $galeri), [
            'photos' => [
                UploadedFile::fake()->image('satu.jpg'),
                UploadedFile::fake()->image('dua.jpg'),
            ],
        ]);

        $response->assertRedirect(route('admin.galeri.edit', $galeri));
        $this->assertSame(2, $galeri->items()->where('type', GaleriItem::TYPE_PHOTO)->count());
    }

    public function test_admin_can_add_youtube_video_to_album(): void
    {
        $user = $this->adminUser();

        $galeri = Galeri::create([
            'user_id' => $user->id,
            'title' => 'Album Video',
            'slug' => 'album-video',
            'status' => Galeri::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($user)->post(route('admin.galeri.youtube.store', $galeri), [
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'title' => 'Video Kegiatan',
        ]);

        $response->assertRedirect(route('admin.galeri.edit', $galeri));
        $this->assertDatabaseHas('galeri_items', [
            'galeri_id' => $galeri->id,
            'type' => GaleriItem::TYPE_YOUTUBE,
            'youtube_video_id' => 'dQw4w9WgXcQ',
            'title' => 'Video Kegiatan',
        ]);
    }

    public function test_invalid_youtube_url_is_rejected(): void
    {
        $user = $this->adminUser();

        $galeri = Galeri::create([
            'user_id' => $user->id,
            'title' => 'Album',
            'slug' => 'album',
            'status' => Galeri::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($user)->post(route('admin.galeri.youtube.store', $galeri), [
            'youtube_url' => 'https://example.com/not-youtube',
        ]);

        $response->assertSessionHasErrors('youtube_url');
    }

    public function test_youtube_helper_extracts_video_id(): void
    {
        $this->assertSame('abc123XYZ01', Youtube::extractVideoId('https://youtu.be/abc123XYZ01'));
        $this->assertSame('abc123XYZ01', Youtube::extractVideoId('https://www.youtube.com/watch?v=abc123XYZ01'));
        $this->assertNull(Youtube::extractVideoId('https://example.com'));
    }

    public function test_admin_can_delete_galeri_media_item(): void
    {
        Storage::fake('public');
        $user = $this->adminUser();

        $galeri = Galeri::create([
            'user_id' => $user->id,
            'title' => 'Album Hapus',
            'slug' => 'album-hapus',
            'status' => Galeri::STATUS_DRAFT,
        ]);

        $item = GaleriItem::create([
            'galeri_id' => $galeri->id,
            'type' => GaleriItem::TYPE_YOUTUBE,
            'youtube_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'youtube_video_id' => 'dQw4w9WgXcQ',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)->delete(route('admin.galeri.media.destroy', [$galeri, $item]));

        $response->assertRedirect(route('admin.galeri.edit', $galeri));
        $this->assertDatabaseMissing('galeri_items', ['id' => $item->id]);
    }
}
