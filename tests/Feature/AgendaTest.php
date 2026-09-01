<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AgendaTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);
        Permission::create(['name' => 'manage agenda', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo(['view dashboard', 'manage agenda']);

        return $user;
    }

    public function test_admin_can_view_agenda_index(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->get(route('admin.agenda.index'));

        $response->assertOk();
        $response->assertSee('Agenda Kegiatan');
    }

    public function test_admin_can_create_agenda_with_time(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->post(route('admin.agenda.store'), [
            'title' => 'Rapat Evaluasi Program',
            'event_type' => Agenda::TYPE_RAPAT,
            'excerpt' => 'Evaluasi program triwulan',
            'content' => '<p>Detail rapat</p>',
            'event_date' => now()->addWeek()->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '11:30',
            'location' => 'Aula Kecamatan',
            'organizer' => 'Kecamatan Sindang Kelingi',
            'contact_name' => 'Admin Sekretariat',
            'contact_phone' => '08123456789',
            'status' => Agenda::STATUS_PUBLISHED,
            'is_featured' => 1,
        ]);

        $response->assertRedirect(route('admin.agenda.index'));
        $this->assertDatabaseHas('agendas', [
            'title' => 'Rapat Evaluasi Program',
            'slug' => 'rapat-evaluasi-program',
            'event_type' => Agenda::TYPE_RAPAT,
            'status' => Agenda::STATUS_PUBLISHED,
            'user_id' => $user->id,
            'is_featured' => true,
        ]);
    }

    public function test_admin_can_create_online_agenda(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->post(route('admin.agenda.store'), [
            'title' => 'Webinar Pelayanan Publik',
            'event_type' => Agenda::TYPE_SOSIALISASI,
            'event_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '14:00',
            'is_online' => 1,
            'online_link' => 'https://zoom.us/j/123456789',
            'status' => Agenda::STATUS_DRAFT,
        ]);

        $response->assertRedirect(route('admin.agenda.index'));
        $this->assertDatabaseHas('agendas', [
            'title' => 'Webinar Pelayanan Publik',
            'is_online' => true,
            'online_link' => 'https://zoom.us/j/123456789',
        ]);
    }

    public function test_admin_edit_agenda_includes_rich_text_editor(): void
    {
        $user = $this->adminUser();

        $agenda = Agenda::create([
            'user_id' => $user->id,
            'title' => 'Kegiatan Test',
            'slug' => 'kegiatan-test',
            'event_type' => Agenda::TYPE_LAINNYA,
            'event_date' => now()->addDay(),
            'start_time' => '10:00',
            'status' => Agenda::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($user)->get(route('admin.agenda.edit', $agenda));

        $response->assertOk();
        $response->assertSee('tinymce.min.js', false);
        $response->assertSee('id="agenda-content"', false);
        $response->assertSee('type="time"', false);
    }

    public function test_end_time_must_be_after_start_time(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->post(route('admin.agenda.store'), [
            'title' => 'Kegiatan Invalid',
            'event_type' => Agenda::TYPE_LAINNYA,
            'event_date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '14:00',
            'end_time' => '10:00',
            'status' => Agenda::STATUS_DRAFT,
        ]);

        $response->assertSessionHasErrors('end_time');
    }

    public function test_admin_can_upload_agenda_editor_image(): void
    {
        $user = $this->adminUser();
        $file = \Illuminate\Http\UploadedFile::fake()->image('foto.jpg', 800, 600);

        $response = $this->actingAs($user)->postJson(route('admin.agenda.editor-upload'), [
            'file' => $file,
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['url', 'location']);
    }
}
