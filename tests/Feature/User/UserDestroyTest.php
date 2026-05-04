<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\InteractsWithRoles;
use Tests\TestCase;

class UserDestroyTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_admin_can_delete_another_user(): void
    {
        Sanctum::actingAs($this->createAdmin());
        $target = $this->createViewer();

        $this->deleteJson("/api/users/{$target->id}")->assertNoContent();

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = $this->createAdmin();
        Sanctum::actingAs($admin);

        $this->deleteJson("/api/users/{$admin->id}")->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_manager_cannot_delete_users(): void
    {
        Sanctum::actingAs($this->createManager());
        $target = $this->createViewer();

        $this->deleteJson("/api/users/{$target->id}")->assertForbidden();
    }

    public function test_viewer_cannot_delete_users(): void
    {
        Sanctum::actingAs($this->createViewer());
        $target = $this->createViewer();

        $this->deleteJson("/api/users/{$target->id}")->assertForbidden();
    }

    public function test_requires_authentication(): void
    {
        $user = $this->createViewer();

        $this->deleteJson("/api/users/{$user->id}")->assertUnauthorized();
    }

    public function test_returns_404_for_nonexistent_user(): void
    {
        Sanctum::actingAs($this->createAdmin());

        $this->deleteJson('/api/users/99999')->assertNotFound();
    }
}
