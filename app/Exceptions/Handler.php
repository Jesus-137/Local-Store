<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'status' => 'Error',
                        'message' => 'Validation error',
                        'errors' => $e->validator->errors()
                    ], 422);
                }

                if ($e instanceof AuthenticationException) {
                    return response()->json([
                        'status' => 'Error',
                        'message' => 'Unauthorized',
                        'errors' => [
                            'token' => 'Invalid or missing token'
                        ]
                    ], 401);
                }

                if ($e instanceof ModelNotFoundException) {
                    return response()->json([
                        'status' => 'Error',
                        'message' => 'Resource not found',
                        'errors' => ['resource' => 'The requested resource was not found']
                    ], 404);
                }

                return response()->json([
                    'status' => 'Error',
                    'message' => $e->getMessage(),
                    'errors' => ['general' => 'An unexpected error occurred']
                ], 500);
            }
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Unauthorized',
                'errors' => [
                    'token' => 'Invalid or missing token'
                ]
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}
