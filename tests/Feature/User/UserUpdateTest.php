<?php

namespace Tests\Feature\User;

use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\InteractsWithRoles;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_admin_can_update_any_user(): void
    {
        Sanctum::actingAs($this->createAdmin());
        $target = $this->createViewer();

        $this->putJson("/api/users/{$target->id}", [
            'name'  => 'Updated Name',
            'email' => $target->email,
            'role'  => UserRole::Viewer->value,
        ])->assertOk()->assertJsonPath('name', 'Updated Name');
    }

    public function test_user_can_update_themselves(): void
    {
        $user = $this->createViewer();
        Sanctum::actingAs($user);

        $this->putJson("/api/users/{$user->id}", [
            'name'  => 'My New Name',
            'email' => $user->email,
            'role'  => UserRole::Viewer->value,
        ])->assertOk()->assertJsonPath('name', 'My New Name');
    }

    public function test_viewer_cannot_update_another_user(): void
    {
        Sanctum::actingAs($this->createViewer());
        $other = $this->createViewer();

        $this->putJson("/api/users/{$other->id}", [
            'name'  => 'Hacked',
            'email' => $other->email,
            'role'  => UserRole::Viewer->value,
        ])->assertForbidden();
    }

    public function test_requires_authentication(): void
    {
        $user = $this->createViewer();

        $this->putJson("/api/users/{$user->id}", [
            'name' => 'No Auth',
        ])->assertUnauthorized();
    }

    public function test_email_can_remain_unchanged_for_same_user(): void
    {
        Sanctum::actingAs($this->createAdmin());
        $target = $this->createViewer(['email' => 'same@example.com']);

        $this->putJson("/api/users/{$target->id}", [
            'name'  => 'Same Email',
            'email' => 'same@example.com',
            'role'  => UserRole::Viewer->value,
        ])->assertOk();
    }

    public function test_email_must_be_unique_across_other_users(): void
    {
        Sanctum::actingAs($this->createAdmin());
        $this->createViewer(['email' => 'taken@example.com']);
        $target = $this->createViewer();

        $this->putJson("/api/users/{$target->id}", [
            'name'  => 'Test',
            'email' => 'taken@example.com',
            'role'  => UserRole::Viewer->value,
        ])->assertUnprocessable()->assertJsonValidationErrors('email');
    }
}
