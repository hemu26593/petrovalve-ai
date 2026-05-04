<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\InteractsWithRoles;
use Tests\TestCase;

class UserShowTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_admin_can_view_any_user(): void
    {
        Sanctum::actingAs($this->createAdmin());
        $target = $this->createViewer();

        $this->getJson("/api/users/{$target->id}")->assertOk()
            ->assertJsonPath('id', $target->id);
    }

    public function test_user_can_view_themselves(): void
    {
        $user = $this->createViewer();
        Sanctum::actingAs($user);

        $this->getJson("/api/users/{$user->id}")->assertOk()
            ->assertJsonPath('id', $user->id);
    }

    public function test_viewer_cannot_view_another_user(): void
    {
        Sanctum::actingAs($this->createViewer());
        $other = $this->createViewer();

        $this->getJson("/api/users/{$other->id}")->assertForbidden();
    }

    public function test_requires_authentication(): void
    {
        $user = $this->createViewer();

        $this->getJson("/api/users/{$user->id}")->assertUnauthorized();
    }

    public function test_returns_404_for_nonexistent_user(): void
    {
        Sanctum::actingAs($this->createAdmin());

        $this->getJson('/api/users/99999')->assertNotFound();
    }
}
