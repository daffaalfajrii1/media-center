<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\BeritaCategory;
use App\Models\User;
use App\Models\Galeri;
use App\Models\Layanan;
use App\Models\LayananField;
use App\Models\LayananPengajuan;
use App\Models\Pengumuman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_uses_binduz_layout(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('binduz/assets/css/style.css', false);
        $response->assertSee('Layanan Online', false);
    }

    public function test_public_module_routes_are_accessible(): void
    {
        $category = BeritaCategory::create([
            'name' => 'Umum',
            'slug' => 'umum',
            'is_active' => true,
        ]);

        $author = User::factory()->create(['name' => 'Editor Berita']);

        $berita = Berita::create([
            'berita_category_id' => $category->id,
            'user_id' => $author->id,
            'title' => 'Berita Publik',
            'slug' => 'berita-publik',
            'content' => '<p>Konten berita.</p>',
            'status' => Berita::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $agenda = Agenda::create([
            'title' => 'Agenda Publik',
            'slug' => 'agenda-publik',
            'content' => '<p>Konten agenda.</p>',
            'event_type' => Agenda::TYPE_LAINNYA,
            'event_date' => now()->addWeek(),
            'start_time' => '09:00',
            'status' => Agenda::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $galeri = Galeri::create([
            'title' => 'Galeri Publik',
            'slug' => 'galeri-publik',
            'description' => 'Dokumentasi kegiatan gotong royong desa.',
            'status' => Galeri::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        Galeri::create([
            'title' => 'Festival Budaya',
            'slug' => 'festival-budaya',
            'description' => 'Pameran seni dan budaya lokal.',
            'status' => Galeri::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $pengumuman = Pengumuman::create([
            'title' => 'Pengumuman Publik',
            'slug' => 'pengumuman-publik',
            'content' => '<p>Isi pengumuman.</p>',
            'status' => Pengumuman::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $layanan = Layanan::create([
            'code' => 'LYN0001',
            'title' => 'Layanan Publik',
            'slug' => 'layanan-publik',
            'description' => 'Deskripsi layanan',
            'is_active' => true,
        ]);

        LayananField::create([
            'layanan_id' => $layanan->id,
            'name' => 'Nama Lengkap',
            'field_key' => 'nama_lengkap',
            'type' => LayananField::TYPE_TEXT,
            'is_required' => true,
        ]);

        $pengajuan = LayananPengajuan::create([
            'layanan_id' => $layanan->id,
            'registration_number' => 'LYN0001-'.now()->year.'-000001',
            'data' => ['nama_lengkap' => 'Andi'],
            'status' => LayananPengajuan::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        $this->get(route('public.berita.index'))->assertOk();
        $this->get(route('public.berita.show', $berita->slug))
            ->assertOk()
            ->assertSee('Berita Publik')
            ->assertSee('Oleh')
            ->assertSee('Editor Berita')
            ->assertSee('berita-like-btn', false);
        $this->get(route('public.agenda.index'))->assertOk();
        $this->get(route('public.agenda.show', $agenda->slug))->assertOk()->assertSee('Agenda Publik');
        $this->get(route('public.galeri.index'))->assertOk()->assertSee('Cari galeri', false);
        $this->get(route('public.galeri.index', ['search' => 'gotong royong']))
            ->assertOk()
            ->assertSee('Galeri Publik')
            ->assertDontSee('Festival Budaya');
        $this->get(route('public.galeri.show', $galeri->slug))->assertOk()->assertSee('Galeri Publik');
        $this->get(route('public.pengumuman.index'))->assertOk();
        $this->get(route('public.pengumuman.show', $pengumuman->slug))->assertOk()->assertSee('Pengumuman Publik');
        $this->get(route('public.layanan.index'))->assertOk();
        $this->get(route('public.layanan.show', $layanan->slug))->assertOk()->assertSee('Formulir Pengajuan');
        $this->get(route('public.layanan.check-status', ['nomor' => $pengajuan->registration_number]))
            ->assertOk()
            ->assertSee($pengajuan->registration_number);
        $this->get(route('public.kontak.index'))->assertOk();
    }

    public function test_public_can_like_berita_once_per_device(): void
    {
        $author = User::factory()->create(['name' => 'Editor Berita']);

        $berita = Berita::create([
            'user_id' => $author->id,
            'title' => 'Berita Suka',
            'slug' => 'berita-suka',
            'content' => '<p>Konten.</p>',
            'status' => Berita::STATUS_PUBLISHED,
            'published_at' => now(),
            'likes_count' => 0,
        ]);

        $visitorToken = (string) \Illuminate\Support\Str::uuid();

        $this->withSession(['berita_visitor_token' => $visitorToken]);

        $this->postJson(route('public.berita.like', $berita->slug))
            ->assertOk()
            ->assertJson(['liked' => true, 'likes_count' => 1]);

        $this->assertDatabaseHas('berita_likes', [
            'berita_id' => $berita->id,
            'visitor_token' => $visitorToken,
        ]);

        $this->postJson(route('public.berita.like', $berita->slug))
            ->assertOk()
            ->assertJson(['liked' => true, 'likes_count' => 1]);

        $berita->refresh();
        $this->assertSame(1, $berita->likes_count);
    }

    public function test_public_can_submit_layanan(): void
    {
        $layanan = Layanan::create([
            'code' => 'LYN0002',
            'title' => 'SKTM',
            'slug' => 'sktm',
            'is_active' => true,
        ]);

        LayananField::create([
            'layanan_id' => $layanan->id,
            'name' => 'Nama',
            'field_key' => 'nama',
            'type' => LayananField::TYPE_TEXT,
            'is_required' => true,
        ]);

        $response = $this->post(route('public.layanan.store', $layanan->slug), [
            'fields' => ['nama' => 'Budi Santoso'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('layanan_pengajuans', [
            'layanan_id' => $layanan->id,
            'status' => LayananPengajuan::STATUS_PENDING,
        ]);
    }

    public function test_public_can_submit_layanan_with_file_upload(): void
    {
        Storage::fake('public');

        $layanan = Layanan::create([
            'code' => 'LYN0003',
            'title' => 'Domisili',
            'slug' => 'domisili',
            'is_active' => true,
        ]);

        LayananField::create([
            'layanan_id' => $layanan->id,
            'name' => 'Nama Lengkap',
            'field_key' => 'nama_lengkap',
            'type' => LayananField::TYPE_TEXT,
            'is_required' => true,
        ]);

        LayananField::create([
            'layanan_id' => $layanan->id,
            'name' => 'Upload KTP',
            'field_key' => 'upload_ktp',
            'type' => LayananField::TYPE_PDF,
            'is_required' => true,
        ]);

        $response = $this->post(route('public.layanan.store', $layanan->slug), [
            'fields' => [
                'nama_lengkap' => 'Andi Wijaya',
                'upload_ktp' => UploadedFile::fake()->create('ktp.pdf', 100, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect();

        $pengajuan = LayananPengajuan::where('layanan_id', $layanan->id)->first();
        $this->assertNotNull($pengajuan);
        $this->assertSame('Andi Wijaya', $pengajuan->data['nama_lengkap']);
        $this->assertNotEmpty($pengajuan->data['upload_ktp']);
    }

    public function test_berita_index_paginates_results(): void
    {
        $author = User::factory()->create();

        for ($i = 1; $i <= 7; $i++) {
            Berita::create([
                'user_id' => $author->id,
                'title' => 'Berita Paginasi '.$i,
                'slug' => 'berita-paginasi-'.$i,
                'content' => '<p>Konten berita '.$i.'.</p>',
                'status' => Berita::STATUS_PUBLISHED,
                'published_at' => now()->subMinutes($i),
            ]);
        }

        $pageOne = $this->get(route('public.berita.index'));
        $pageOne->assertOk();
        $pageOne->assertSee('Berita Paginasi 1');
        $pageOne->assertSee('Berita Paginasi 6');
        $pageOne->assertSee('binduz-er-pagination-nav', false);
        $pageOne->assertSee('Menampilkan 1–6 dari 7 berita');
        $pageOne->assertSee('page=2', false);

        $pageTwo = $this->get(route('public.berita.index', ['page' => 2]));
        $pageTwo->assertOk();
        $pageTwo->assertSee('Berita Paginasi 7');
        $pageTwo->assertSee('Menampilkan 7–7 dari 7 berita');
    }
}
