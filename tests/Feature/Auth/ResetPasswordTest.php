<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\Feature\Concerns\InteractsWithRoles;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_user_can_reset_password(): void
    {
        $user  = $this->createViewer();
        $token = Password::createToken($user);

        $this->postJson('/api/auth/reset-password', [
            'email'                 => $user->email,
            'token'                 => $token,
            'password'              => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])->assertOk()->assertJsonPath('message', 'Password reset successfully.');

        $this->assertTrue(Hash::check('NewPassword1!', $user->fresh()->password));
    }

    public function test_fails_with_invalid_token(): void
    {
        $user = $this->createViewer();

        $this->postJson('/api/auth/reset-password', [
            'email'                 => $user->email,
            'token'                 => 'invalid-token',
            'password'              => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_revokes_all_tokens_after_reset(): void
    {
        $user  = $this->createViewer();
        $user->createToken('device-1');
        $user->createToken('device-2');
        $token = Password::createToken($user);

        $this->assertDatabaseCount('personal_access_tokens', 2);

        $this->postJson('/api/auth/reset-password', [
            'email'                 => $user->email,
            'token'                 => $token,
            'password'              => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_requires_email_token_and_password(): void
    {
        $this->postJson('/api/auth/reset-password', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'token', 'password']);
    }
}
