<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'view dashboard', 'manage users', 'manage berita', 'manage pengumuman', 'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'operator', 'guard_name' => 'web']);
    }

    private function adminUser(): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->givePermissionTo(['view dashboard', 'manage users', 'manage berita', 'manage pengumuman']);

        return $user;
    }

    public function test_admin_can_view_users_index(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->get(route('admin.pengguna.index'));

        $response->assertOk();
        $response->assertSee('Pengguna');
    }

    public function test_admin_can_create_user_with_custom_permissions(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->post(route('admin.pengguna.store'), [
            'name' => 'Editor Berita',
            'email' => 'editor.berita@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'custom',
            'permissions' => ['view dashboard', 'manage berita'],
        ]);

        $response->assertRedirect(route('admin.pengguna.index'));
        $created = User::where('email', 'editor.berita@example.com')->first();
        $this->assertTrue($created->can('manage berita'));
        $this->assertFalse($created->can('manage pengumuman'));
    }

    public function test_admin_can_create_user_with_operator_template(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->post(route('admin.pengguna.store'), [
            'name' => 'Operator Baru',
            'email' => 'operator@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => User::ROLE_OPERATOR,
            'permissions' => ['view dashboard', 'manage berita', 'manage pengumuman'],
        ]);

        $response->assertRedirect(route('admin.pengguna.index'));
        $created = User::where('email', 'operator@example.com')->first();
        $this->assertTrue($created->hasRole(User::ROLE_OPERATOR));
        $this->assertTrue($created->can('manage berita'));
    }

    public function test_admin_cannot_delete_self(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->delete(route('admin.pengguna.destroy', $user));

        $response->assertRedirect(route('admin.pengguna.index'));
        $response->assertSessionHas('error');
    }

    public function test_non_manager_cannot_access_users_module(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->givePermissionTo('view dashboard');

        $this->actingAs($user)->get(route('admin.pengguna.index'))->assertForbidden();
    }
}
