<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $response = $this->authService->register($request->validated());
        return $this->successResponse($response, 'User registered successfully');
    }

    public function login(LoginRequest $request)
    {
        $response = $this->authService->login($request->validated());

        if (!$response) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        return $this->successResponse($response, 'Login successful');
    }
}
