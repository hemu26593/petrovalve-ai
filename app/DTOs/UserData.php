<?php

namespace App\DTOs;

use App\Enums\UserRole;

readonly class UserData
{
    public function __construct(
        public string   $name,
        public string   $email,
        public ?string  $password = null,
        public UserRole $role = UserRole::Viewer,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name:     $data['name'],
            email:    $data['email'],
            password: $data['password'] ?? null,
            role:     UserRole::from($data['role'] ?? UserRole::Viewer->value),
        );
    }
}
