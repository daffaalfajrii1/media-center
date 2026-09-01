<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'view dashboard', 'manage users', 'manage roles', 'manage berita',
            'manage agenda', 'manage galeri', 'manage profil', 'manage pengumuman',
            'manage layanan', 'manage pengaturan',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        foreach ([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN, User::ROLE_EDITOR, User::ROLE_OPERATOR] as $roleName) {
            Role::create(['name' => $roleName, 'guard_name' => 'web']);
        }

        Role::findByName(User::ROLE_SUPER_ADMIN, 'web')->givePermissionTo(Permission::all());
        Role::findByName(User::ROLE_ADMIN, 'web')->givePermissionTo([
            'view dashboard', 'manage users', 'manage berita', 'manage agenda',
            'manage galeri', 'manage profil', 'manage pengumuman', 'manage layanan',
        ]);
        Role::findByName(User::ROLE_EDITOR, 'web')->givePermissionTo([
            'view dashboard', 'manage berita', 'manage agenda', 'manage galeri', 'manage pengumuman',
        ]);
        Role::findByName(User::ROLE_OPERATOR, 'web')->givePermissionTo([
            'view dashboard', 'manage berita', 'manage pengumuman',
        ]);
    }

    private function superAdmin(): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole(User::ROLE_SUPER_ADMIN);

        return $user;
    }

    public function test_super_admin_can_view_roles_index(): void
    {
        $response = $this->actingAs($this->superAdmin())->get(route('admin.roles.index'));

        $response->assertOk();
        $response->assertSee('Role & Permission');
        $response->assertSee('Role bersifat tetap');
    }

    public function test_user_without_manage_roles_cannot_access_roles(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->givePermissionTo('view dashboard');

        $response = $this->actingAs($user)->get(route('admin.roles.index'));

        $response->assertForbidden();
    }

    public function test_super_admin_can_update_editor_role_permissions(): void
    {
        $response = $this->actingAs($this->superAdmin())->put(route('admin.roles.update', ['role' => User::ROLE_EDITOR]), [
            'permissions' => ['view dashboard', 'manage berita', 'manage profil'],
        ]);

        $response->assertRedirect(route('admin.roles.index'));

        $editor = Role::findByName(User::ROLE_EDITOR, 'web');
        $this->assertTrue($editor->hasPermissionTo('manage berita'));
        $this->assertTrue($editor->hasPermissionTo('manage profil'));
        $this->assertFalse($editor->hasPermissionTo('manage agenda'));
    }

    public function test_dashboard_permission_is_always_included(): void
    {
        $this->actingAs($this->superAdmin())->put(route('admin.roles.update', ['role' => User::ROLE_OPERATOR]), [
            'permissions' => ['manage berita'],
        ]);

        $operator = Role::findByName(User::ROLE_OPERATOR, 'web');
        $this->assertTrue($operator->hasPermissionTo('view dashboard'));
    }

    public function test_admin_user_sees_admin_profile_page(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->givePermissionTo('view dashboard');

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('Profil Saya');
        $response->assertSee('Ubah Password');
    }
}
