<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\InteractsWithRoles;
use Tests\TestCase;

class UserIndexTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpRoles();
    }

    public function test_admin_can_list_users(): void
    {
        Sanctum::actingAs($this->createAdmin());

        $this->getJson('/api/users')->assertOk();
    }

    public function test_manager_can_list_users(): void
    {
        Sanctum::actingAs($this->createManager());

        $this->getJson('/api/users')->assertOk();
    }

    public function test_viewer_cannot_list_users(): void
    {
        Sanctum::actingAs($this->createViewer());

        $this->getJson('/api/users')->assertForbidden();
    }

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/users')->assertUnauthorized();
    }

    public function test_returns_paginated_results(): void
    {
        Sanctum::actingAs($this->createAdmin());
        $this->createViewer();
        $this->createViewer();

        $this->getJson('/api/users')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'current_page', 'total']);
    }
}
