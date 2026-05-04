<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\InteractsWithRoles;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_user_can_login(): void
    {
        $user = $this->createViewer(['password' => bcrypt('Password1!')]);

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'Password1!',
        ])->assertOk()->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token']);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = $this->createViewer();

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    }

    public function test_login_fails_with_unknown_email(): void
    {
        $this->postJson('/api/auth/login', [
            'email'    => 'nobody@example.com',
            'password' => 'Password1!',
        ])->assertUnauthorized();
    }

    public function test_login_requires_email(): void
    {
        $this->postJson('/api/auth/login', [
            'password' => 'Password1!',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_login_requires_password(): void
    {
        $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
        ])->assertUnprocessable()->assertJsonValidationErrors('password');
    }
}
