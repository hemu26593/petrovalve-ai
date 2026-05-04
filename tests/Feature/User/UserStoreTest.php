<?php

namespace Tests\Feature\User;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\InteractsWithRoles;
use Tests\TestCase;

class UserStoreTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name'                  => 'Jane Doe',
            'email'                 => 'jane@example.com',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
            'role'                  => UserRole::Viewer->value,
        ], $overrides);
    }

    public function test_admin_can_create_user(): void
    {
        Sanctum::actingAs($this->createAdmin());

        $this->postJson('/api/users', $this->validPayload())
            ->assertCreated()
            ->assertJsonPath('email', 'jane@example.com');
    }

    public function test_manager_cannot_create_user(): void
    {
        Sanctum::actingAs($this->createManager());

        $this->postJson('/api/users', $this->validPayload())->assertForbidden();
    }

    public function test_viewer_cannot_create_user(): void
    {
        Sanctum::actingAs($this->createViewer());

        $this->postJson('/api/users', $this->validPayload())->assertForbidden();
    }

    public function test_requires_authentication(): void
    {
        $this->postJson('/api/users', $this->validPayload())->assertUnauthorized();
    }

    public function test_requires_name_email_and_password(): void
    {
        Sanctum::actingAs($this->createAdmin());

        $this->postJson('/api/users', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_email_must_be_unique(): void
    {
        Sanctum::actingAs($this->createAdmin());
        $this->createViewer(['email' => 'jane@example.com']);

        $this->postJson('/api/users', $this->validPayload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_assigns_specified_role(): void
    {
        Sanctum::actingAs($this->createAdmin());

        $this->postJson('/api/users', $this->validPayload(['role' => UserRole::Manager->value]))
            ->assertCreated();

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertTrue($user->hasRole(UserRole::Manager->value));
    }

    public function test_defaults_to_viewer_when_no_role_specified(): void
    {
        Sanctum::actingAs($this->createAdmin());

        $payload = $this->validPayload();
        unset($payload['role']);

        $this->postJson('/api/users', $payload)->assertCreated();

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertTrue($user->hasRole(UserRole::Viewer->value));
    }
}
