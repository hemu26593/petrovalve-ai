<?php

namespace App\Services;

use App\DTOs\LoginData;
use App\DTOs\RegisterData;
use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly UserRepository $repository,
    ) {}

    public function register(RegisterData $data): array
    {
        $user = $this->repository->create([
            'name'     => $data->name,
            'email'    => $data->email,
            'password' => Hash::make($data->password),
        ]);

        $user->assignRole(UserRole::Viewer->value);

        $token = $user->createToken('api')->plainTextToken;

        return ['user' => $user->load('roles'), 'token' => $token];
    }

    public function login(LoginData $data): array
    {
        $user = $this->repository->findByEmail($data->email);

        if (! $user || ! Hash::check($data->password, $user->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        $token = $user->createToken('api')->plainTextToken;

        return ['user' => $user->load('roles'), 'token' => $token];
    }

    public function forgotPassword(string $email): void
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== PasswordBroker::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }

    public function resetPassword(string $email, string $token, string $newPassword): void
    {
        $status = Password::reset(
            ['email' => $email, 'token' => $token, 'password' => $newPassword],
            function (User $user, string $password) {
                $this->repository->update($user, ['password' => Hash::make($password)]);
                $user->tokens()->delete();
            },
        );

        if ($status !== PasswordBroker::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }
    }

    public function changePassword(User $user, string $newPassword): void
    {
        $this->repository->update($user, ['password' => Hash::make($newPassword)]);
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
