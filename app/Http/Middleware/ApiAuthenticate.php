<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class ApiAuthenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }

    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json([
            'status' => 'Error',
            'message' => 'Unauthenticated',
            'errors' => [
                'token' => 'Authentication required'
            ]
        ], 401));
    }
}
