<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\InteractsWithRoles;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_user_can_change_password(): void
    {
        $user = $this->createViewer(['password' => Hash::make('OldPassword1!')]);
        Sanctum::actingAs($user);

        $this->putJson('/api/auth/password', [
            'current_password'      => 'OldPassword1!',
            'password'              => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])->assertOk();

        $this->assertTrue(Hash::check('NewPassword1!', $user->fresh()->password));
    }

    public function test_fails_with_wrong_current_password(): void
    {
        $user = $this->createViewer(['password' => Hash::make('OldPassword1!')]);
        Sanctum::actingAs($user);

        $this->putJson('/api/auth/password', [
            'current_password'      => 'WrongPassword1!',
            'password'              => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])->assertUnprocessable()->assertJsonValidationErrors('current_password');
    }

    public function test_new_password_must_differ_from_current(): void
    {
        $user = $this->createViewer(['password' => Hash::make('SamePassword1!')]);
        Sanctum::actingAs($user);

        $this->putJson('/api/auth/password', [
            'current_password'      => 'SamePassword1!',
            'password'              => 'SamePassword1!',
            'password_confirmation' => 'SamePassword1!',
        ])->assertUnprocessable()->assertJsonValidationErrors('password');
    }

    public function test_requires_password_confirmation(): void
    {
        $user = $this->createViewer(['password' => Hash::make('OldPassword1!')]);
        Sanctum::actingAs($user);

        $this->putJson('/api/auth/password', [
            'current_password' => 'OldPassword1!',
            'password'         => 'NewPassword1!',
        ])->assertUnprocessable()->assertJsonValidationErrors('password');
    }

    public function test_requires_authentication(): void
    {
        $this->putJson('/api/auth/password', [
            'current_password'      => 'OldPassword1!',
            'password'              => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])->assertUnauthorized();
    }
}
