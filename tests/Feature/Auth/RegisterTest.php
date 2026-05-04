<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\InteractsWithRoles;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token']);
    }

    public function test_registered_user_receives_viewer_role(): void
    {
        $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
        $user = \App\Models\User::where('email', 'john@example.com')->first();
        $this->assertTrue($user->hasRole(UserRole::Viewer->value));
    }

    public function test_register_requires_name(): void
    {
        $this->postJson('/api/auth/register', [
            'email'                 => 'john@example.com',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
        ])->assertUnprocessable()->assertJsonValidationErrors('name');
    }

    public function test_register_requires_valid_email(): void
    {
        $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe',
            'email'                 => 'not-an-email',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_register_requires_unique_email(): void
    {
        $this->createViewer(['email' => 'john@example.com']);

        $this->postJson('/api/auth/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'Password1!',
            'password_confirmation' => 'Password1!',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_register_requires_password(): void
    {
        $this->postJson('/api/auth/register', [
            'name'  => 'John Doe',
            'email' => 'john@example.com',
        ])->assertUnprocessable()->assertJsonValidationErrors('password');
    }

    public function test_register_requires_password_confirmation(): void
    {
        $this->postJson('/api/auth/register', [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => 'Password1!',
        ])->assertUnprocessable()->assertJsonValidationErrors('password');
    }
}
