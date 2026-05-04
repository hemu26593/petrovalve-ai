<?php

namespace App\Services;

use App\DTOs\UserData;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private readonly UserRepository $repository,
    ) {}

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function create(UserData $data): User
    {
        $user = $this->repository->create([
            'name'     => $data->name,
            'email'    => $data->email,
            'password' => Hash::make($data->password),
        ]);

        $user->assignRole($data->role->value);

        return $user;
    }

    public function update(User $user, UserData $data): User
    {
        $attributes = [
            'name'  => $data->name,
            'email' => $data->email,
        ];

        if ($data->password !== null) {
            $attributes['password'] = Hash::make($data->password);
        }

        $user = $this->repository->update($user, $attributes);

        $user->syncRoles([$data->role->value]);

        return $user;
    }

    public function delete(User $user): void
    {
        $this->repository->delete($user);
    }
}
