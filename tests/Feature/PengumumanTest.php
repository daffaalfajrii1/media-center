<?php

namespace Tests\Feature;

use App\Models\Pengumuman;
use App\Models\PengumumanAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PengumumanTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage pengumuman', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo(['view dashboard', 'manage pengumuman']);

        return $user;
    }

    public function test_admin_can_view_pengumuman_index(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->get(route('admin.pengumuman.index'));

        $response->assertOk();
        $response->assertSee('Pengumuman');
    }

    public function test_admin_can_create_pengumuman(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->post(route('admin.pengumuman.store'), [
            'title' => 'Pengumuman Seleksi CPNS',
            'excerpt' => 'Informasi seleksi CPNS',
            'content' => '<p>Detail pengumuman</p>',
            'priority' => Pengumuman::PRIORITY_PENTING,
            'status' => Pengumuman::STATUS_PUBLISHED,
            'valid_until' => now()->addMonth()->format('Y-m-d'),
            'is_featured' => 1,
        ]);

        $pengumuman = Pengumuman::where('title', 'Pengumuman Seleksi CPNS')->first();
        $response->assertRedirect(route('admin.pengumuman.edit', $pengumuman));
        $this->assertDatabaseHas('pengumumans', [
            'title' => 'Pengumuman Seleksi CPNS',
            'slug' => 'pengumuman-seleksi-cpns',
            'priority' => Pengumuman::PRIORITY_PENTING,
            'status' => Pengumuman::STATUS_PUBLISHED,
            'user_id' => $user->id,
        ]);
    }

    public function test_admin_can_upload_pdf_attachments(): void
    {
        Storage::fake('public');
        $user = $this->adminUser();

        $pengumuman = Pengumuman::create([
            'user_id' => $user->id,
            'title' => 'Pengumuman PDF',
            'slug' => 'pengumuman-pdf',
            'status' => Pengumuman::STATUS_DRAFT,
            'priority' => Pengumuman::PRIORITY_NORMAL,
        ]);

        $response = $this->actingAs($user)->post(route('admin.pengumuman.lampiran.store', $pengumuman), [
            'files' => [
                UploadedFile::fake()->create('surat-edaran.pdf', 120, 'application/pdf'),
                UploadedFile::fake()->create('formulir.pdf', 80, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect(route('admin.pengumuman.edit', $pengumuman));
        $this->assertSame(2, $pengumuman->attachments()->count());
    }

    public function test_non_pdf_upload_is_rejected(): void
    {
        Storage::fake('public');
        $user = $this->adminUser();

        $pengumuman = Pengumuman::create([
            'user_id' => $user->id,
            'title' => 'Pengumuman',
            'slug' => 'pengumuman',
            'status' => Pengumuman::STATUS_DRAFT,
            'priority' => Pengumuman::PRIORITY_NORMAL,
        ]);

        $response = $this->actingAs($user)->post(route('admin.pengumuman.lampiran.store', $pengumuman), [
            'files' => [
                UploadedFile::fake()->image('bukan-pdf.jpg'),
            ],
        ]);

        $response->assertSessionHasErrors('files.0');
    }

    public function test_admin_edit_pengumuman_includes_rich_text_editor(): void
    {
        $user = $this->adminUser();

        $pengumuman = Pengumuman::create([
            'user_id' => $user->id,
            'title' => 'Test',
            'slug' => 'test',
            'content' => '<p>Isi</p>',
            'status' => Pengumuman::STATUS_DRAFT,
            'priority' => Pengumuman::PRIORITY_NORMAL,
        ]);

        $response = $this->actingAs($user)->get(route('admin.pengumuman.edit', $pengumuman));

        $response->assertOk();
        $response->assertSee('tinymce.min.js', false);
        $response->assertSee('File Unduhan (PDF)');
    }

    public function test_admin_can_delete_pdf_attachment(): void
    {
        Storage::fake('public');
        $user = $this->adminUser();

        $pengumuman = Pengumuman::create([
            'user_id' => $user->id,
            'title' => 'Hapus Lampiran',
            'slug' => 'hapus-lampiran',
            'status' => Pengumuman::STATUS_DRAFT,
            'priority' => Pengumuman::PRIORITY_NORMAL,
        ]);

        $path = UploadedFile::fake()->create('doc.pdf', 50, 'application/pdf')->store("pengumumans/{$pengumuman->id}/pdf", 'public');

        $attachment = PengumumanAttachment::create([
            'pengumuman_id' => $pengumuman->id,
            'title' => 'Dokumen',
            'file_path' => $path,
            'original_name' => 'doc.pdf',
            'file_size' => 51200,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)->delete(route('admin.pengumuman.lampiran.destroy', [$pengumuman, $attachment]));

        $response->assertRedirect(route('admin.pengumuman.edit', $pengumuman));
        $this->assertDatabaseMissing('pengumuman_attachments', ['id' => $attachment->id]);
    }
}
