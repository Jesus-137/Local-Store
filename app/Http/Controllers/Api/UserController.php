<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\ApiResponse;

class UserController extends Controller
{
    use ApiResponse;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAll();
        return $this->successResponse(
            UserResource::collection($users),
            'Users retrieved successfully'
        );
    }

    public function store(UserRequest $request)
    {
        $user = $this->userService->create($request->validated());
        return $this->successResponse(
            new UserResource($user),
            'User created successfully',
            201
        );
    }

    public function show(string $id)
    {
        $user = $this->userService->findById($id);
        return $this->successResponse(
            new UserResource($user),
            'User retrieved successfully'
        );
    }

    public function update(UserRequest $request, string $id)
    {
        $user = $this->userService->update($id, $request->validated());
        return $this->successResponse(
            new UserResource($user),
            'User updated successfully'
        );
    }

    public function destroy(string $id)
    {
        $this->userService->delete($id);
        return $this->successResponse(
            null,
            'User deleted successfully'
        );
    }
}
