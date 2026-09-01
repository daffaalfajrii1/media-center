<?php

namespace Tests\Feature;

use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PegawaiManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage pegawai', 'guard_name' => 'web']);
    }

    private function manager(): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->givePermissionTo(['view dashboard', 'manage pegawai']);

        return $user;
    }

    public function test_manager_can_create_jabatan_and_pegawai(): void
    {
        Storage::fake('public');

        $this->actingAs($this->manager())->post(route('admin.jabatan.store'), [
            'name' => 'Camat',
            'sort_order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.jabatan.index'));

        $jabatan = Jabatan::first();

        $response = $this->actingAs($this->manager())->post(route('admin.pegawai.store'), [
            'jabatan_id' => $jabatan->id,
            'name' => 'Budi Santoso',
            'nip' => '123456',
            'is_published' => '1',
            'photo' => UploadedFile::fake()->image('pegawai.jpg', 400, 500),
        ]);

        $response->assertRedirect(route('admin.pegawai.index'));

        $pegawai = Pegawai::first();
        $this->assertSame('Budi Santoso', $pegawai->name);
        $this->assertNotNull($pegawai->photo_path);
        Storage::disk('public')->assertExists($pegawai->photo_path);
    }

    public function test_public_pegawai_page_shows_published_staff(): void
    {
        $jabatan = Jabatan::create([
            'name' => 'Camat',
            'slug' => 'camat',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Pegawai::create([
            'jabatan_id' => $jabatan->id,
            'name' => 'Ahmad Publik',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        Pegawai::create([
            'jabatan_id' => $jabatan->id,
            'name' => 'Rahasia Draft',
            'is_published' => false,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('public.pegawai.index'));

        $response->assertOk();
        $response->assertSee('Ahmad Publik');
        $response->assertDontSee('Rahasia Draft');
        $response->assertSee('Camat');
    }

    public function test_jabatan_with_pegawai_cannot_be_deleted(): void
    {
        $jabatan = Jabatan::create([
            'name' => 'Staf',
            'slug' => 'staf',
            'is_active' => true,
        ]);

        Pegawai::create([
            'jabatan_id' => $jabatan->id,
            'name' => 'Pegawai A',
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->manager())->delete(route('admin.jabatan.destroy', $jabatan));

        $response->assertRedirect(route('admin.jabatan.index'));
        $this->assertNotNull($jabatan->fresh());
    }
}
