<?php

namespace Tests\Feature\Auth;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Concerns\InteractsWithRoles;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_sends_password_reset_link(): void
    {
        Notification::fake();

        $user = $this->createViewer();

        $this->postJson('/api/auth/forgot-password', ['email' => $user->email])
            ->assertOk()
            ->assertJsonPath('message', 'Password reset link sent to your email.');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_fails_for_unknown_email(): void
    {
        $this->postJson('/api/auth/forgot-password', ['email' => 'nobody@example.com'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_requires_email(): void
    {
        $this->postJson('/api/auth/forgot-password', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }
}
