<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ApiTokenAdminTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        Permission::findOrCreate('view dashboard', 'web');
        Permission::findOrCreate('manage api tokens', 'web');

        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->givePermissionTo(['view dashboard', 'manage api tokens']);

        return $user;
    }

    public function test_admin_can_view_api_tokens_page(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->get(route('admin.api-tokens.index'));

        $response->assertOk();
        $response->assertSee('Token API');
        $response->assertSee('Buat Token Baru');
        $response->assertSee('/api/v1', false);
    }

    public function test_guest_cannot_view_api_tokens_page(): void
    {
        $this->get(route('admin.api-tokens.index'))->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_view_api_tokens_page(): void
    {
        Permission::findOrCreate('view dashboard', 'web');
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->givePermissionTo('view dashboard');

        $this->actingAs($user)
            ->get(route('admin.api-tokens.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_api_token(): void
    {
        $user = $this->adminUser();

        $response = $this->actingAs($user)->post(route('admin.api-tokens.store'), [
            'name' => 'wordpress',
        ]);

        $response->assertRedirect(route('admin.api-tokens.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('api_token_plain');
        $response->assertSessionHas('api_token_name', 'wordpress');

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'wordpress',
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);
    }

    public function test_admin_can_revoke_api_token(): void
    {
        $user = $this->adminUser();
        $token = $user->createToken('wordpress');
        $tokenId = $token->accessToken->id;

        $response = $this->actingAs($user)->delete(route('admin.api-tokens.destroy', $tokenId));

        $response->assertRedirect(route('admin.api-tokens.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);
        $this->assertSame(0, PersonalAccessToken::query()->count());
    }

    public function test_created_token_can_access_berita_api(): void
    {
        $user = $this->adminUser();
        $plain = $user->createToken('wordpress')->plainTextToken;

        $this->withToken($plain)
            ->getJson('/api/v1/berita/terbaru')
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}
