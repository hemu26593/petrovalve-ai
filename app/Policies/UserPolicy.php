<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $auth): bool
    {
        return $auth->hasAnyRole([UserRole::Admin->value, UserRole::Manager->value]);
    }

    public function view(User $auth, User $user): bool
    {
        return $auth->hasRole(UserRole::Admin->value) || $auth->id === $user->id;
    }

    public function create(User $auth): bool
    {
        return $auth->hasRole(UserRole::Admin->value);
    }

    public function update(User $auth, User $user): bool
    {
        return $auth->hasRole(UserRole::Admin->value) || $auth->id === $user->id;
    }

    public function delete(User $auth, User $user): bool
    {
        return $auth->hasRole(UserRole::Admin->value) && $auth->id !== $user->id;
    }
}
