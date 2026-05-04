<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\InteractsWithRoles;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_user_can_logout(): void
    {
        $user = $this->createViewer();
        Sanctum::actingAs($user);

        $this->postJson('/api/auth/logout')->assertOk()
            ->assertJsonPath('message', 'Logged out successfully.');
    }

    public function test_logout_deletes_current_token(): void
    {
        $user       = $this->createViewer();
        $plainToken = $user->createToken('api')->plainTextToken;

        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->withToken($plainToken)->postJson('/api/auth/logout')->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/auth/logout')->assertUnauthorized();
    }
}
