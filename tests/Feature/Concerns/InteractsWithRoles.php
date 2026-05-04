<?php

namespace Tests\Feature\Concerns;

use App\Enums\UserRole;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

trait InteractsWithRoles
{
    protected function setUpRoles(): void
    {
        $this->app[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (UserRole::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
    }

    protected function createAdmin(?array $attributes = []): User
    {
        return User::factory()->create($attributes)->assignRole(UserRole::Admin->value);
    }

    protected function createManager(?array $attributes = []): User
    {
        return User::factory()->create($attributes)->assignRole(UserRole::Manager->value);
    }

    protected function createViewer(?array $attributes = []): User
    {
        return User::factory()->create($attributes)->assignRole(UserRole::Viewer->value);
    }
}
