<?php

namespace App\Http\Controllers;

use App\DTOs\LoginData;
use App\DTOs\RegisterData;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $service,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->service->register(RegisterData::fromArray($request->validated()));

        return response()->json($result, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->service->login(LoginData::fromArray($request->validated()));

        return response()->json($result);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->service->forgotPassword($request->validated('email'));

        return response()->json(['message' => 'Password reset link sent to your email.']);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $this->service->resetPassword($validated['email'], $validated['token'], $validated['password']);

        return response()->json(['message' => 'Password reset successfully.']);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->service->changePassword($request->user(), $request->validated('password'));

        return response()->json(['message' => 'Password changed successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('roles'));
    }

    public function logout(Request $request): JsonResponse
    {
        $this->service->logout($request->user());

        return response()->json(['message' => 'Logged out successfully.']);
    }
}
