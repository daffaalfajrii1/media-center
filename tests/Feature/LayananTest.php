<?php

namespace Tests\Feature;

use App\Models\Layanan;
use App\Models\LayananField;
use App\Models\LayananPengajuan;
use App\Models\User;
use App\Support\LayananRegistrationNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class LayananTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage layanan', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo(['view dashboard', 'manage layanan']);

        return $user;
    }

    public function test_admin_can_view_layanan_index(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->get(route('admin.layanan.index'));

        $response->assertOk();
        $response->assertSee('Layanan Publik');
    }

    public function test_admin_can_create_layanan_with_auto_code(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->post(route('admin.layanan.store'), [
            'title' => 'Surat Keterangan Domisili',
            'description' => 'Layanan domisili mandiri',
            'is_active' => 1,
        ]);

        $layanan = Layanan::where('title', 'Surat Keterangan Domisili')->first();
        $response->assertRedirect(route('admin.layanan.edit', $layanan));
        $this->assertMatchesRegularExpression('/^LYN\d{4}$/', $layanan->code);
        $this->assertDatabaseHas('layanans', [
            'code' => $layanan->code,
            'title' => 'Surat Keterangan Domisili',
            'user_id' => $user->id,
        ]);
    }

    public function test_layanan_codes_are_generated_sequentially(): void
    {
        $this->assertSame('LYN0001', Layanan::generateCode());

        Layanan::create([
            'code' => 'LYN0001',
            'title' => 'Layanan 1',
            'slug' => 'layanan-1',
            'is_active' => true,
        ]);

        $this->assertSame('LYN0002', Layanan::generateCode());
    }

    public function test_admin_can_add_dynamic_fields(): void
    {
        $user = $this->adminUser();

        $layanan = Layanan::create([
            'code' => 'LYN0001',
            'title' => 'Domisili',
            'slug' => 'domisili',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.layanan.fields.store', $layanan), [
            'name' => 'Upload KTP',
            'type' => LayananField::TYPE_PDF,
            'is_required' => 1,
            'help_text' => 'Format PDF',
        ]);

        $response->assertRedirect(route('admin.layanan.edit', $layanan));
        $this->assertDatabaseHas('layanan_fields', [
            'layanan_id' => $layanan->id,
            'name' => 'Upload KTP',
            'field_key' => 'upload_ktp',
            'type' => LayananField::TYPE_PDF,
        ]);
    }

    public function test_registration_number_format(): void
    {
        $layanan = Layanan::create([
            'code' => 'LYN0001',
            'title' => 'Domisili',
            'slug' => 'domisili',
            'is_active' => true,
        ]);

        $number = LayananRegistrationNumber::generate($layanan);

        $this->assertMatchesRegularExpression('/^LYN0001-\d{4}-\d{6}$/', $number);
    }

    public function test_admin_can_complete_pengajuan_with_file_upload(): void
    {
        Storage::fake('public');
        $user = $this->adminUser();

        $layanan = Layanan::create([
            'code' => 'LYN0001',
            'title' => 'Domisili',
            'slug' => 'domisili',
            'is_active' => true,
        ]);

        $pengajuan = LayananPengajuan::create([
            'layanan_id' => $layanan->id,
            'registration_number' => 'LYN0001-'.now()->year.'-000001',
            'data' => ['nama_lengkap' => 'Andi Wijaya'],
            'status' => LayananPengajuan::STATUS_PROCESSING,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($user)->patch(route('admin.layanan-pengajuan.status', $pengajuan), [
            'status' => LayananPengajuan::STATUS_COMPLETED,
            'completion_method' => LayananPengajuan::COMPLETION_FILE_UPLOAD,
            'result_file' => UploadedFile::fake()->create('surat-domisili.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('admin.layanan-pengajuan.show', $pengajuan));
        $pengajuan->refresh();
        $this->assertSame(LayananPengajuan::STATUS_COMPLETED, $pengajuan->status);
        $this->assertSame(LayananPengajuan::COMPLETION_FILE_UPLOAD, $pengajuan->completion_method);
        $this->assertNotNull($pengajuan->result_file_path);
    }

    public function test_admin_can_delete_layanan_with_pengajuans(): void
    {
        Storage::fake('public');
        $user = $this->adminUser();

        $layanan = Layanan::create([
            'code' => 'LYN0099',
            'title' => 'Layanan Uji Hapus',
            'slug' => 'layanan-uji-hapus',
            'is_active' => true,
        ]);

        $pengajuan = LayananPengajuan::create([
            'layanan_id' => $layanan->id,
            'registration_number' => 'LYN0099-'.now()->year.'-000099',
            'data' => ['nama_lengkap' => 'Citra'],
            'status' => LayananPengajuan::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($user)->delete(route('admin.layanan.destroy', $layanan));

        $response->assertRedirect(route('admin.layanan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('layanans', ['id' => $layanan->id]);
        $this->assertDatabaseMissing('layanan_pengajuans', ['id' => $pengajuan->id]);
    }

    public function test_admin_can_complete_pengajuan_with_office_pickup(): void
    {
        $user = $this->adminUser();

        $layanan = Layanan::create([
            'code' => 'LYN0002',
            'title' => 'SKTM',
            'slug' => 'sktm',
            'is_active' => true,
        ]);

        $pengajuan = LayananPengajuan::create([
            'layanan_id' => $layanan->id,
            'registration_number' => 'LYN0002-'.now()->year.'-000001',
            'data' => ['nama_lengkap' => 'Budi'],
            'status' => LayananPengajuan::STATUS_PROCESSING,
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($user)->patch(route('admin.layanan-pengajuan.status', $pengajuan), [
            'status' => LayananPengajuan::STATUS_COMPLETED,
            'completion_method' => LayananPengajuan::COMPLETION_OFFICE_PICKUP,
            'completion_message' => 'Silakan ambil di loket kecamatan dengan membawa KTP.',
        ]);

        $response->assertRedirect(route('admin.layanan-pengajuan.show', $pengajuan));
        $this->assertDatabaseHas('layanan_pengajuans', [
            'id' => $pengajuan->id,
            'status' => LayananPengajuan::STATUS_COMPLETED,
            'completion_method' => LayananPengajuan::COMPLETION_OFFICE_PICKUP,
        ]);
    }
}
