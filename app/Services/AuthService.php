<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data)
    {
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            return [
                'user' => $user,
                'token' => $user->createToken('auth-token')->plainTextToken
            ];
        } catch (\Exception $e) {
            throw new \Exception('Error registering user: ' . $e->getMessage());
        }
    }

    public function login(array $credentials)
    {
        try {
            $user = User::where('email', $credentials['email'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return null;
            }

            return [
                'user' => $user,
                'token' => $user->createToken('auth-token')->plainTextToken
            ];
        } catch (\Exception $e) {
            throw new \Exception('Error during login: ' . $e->getMessage());
        }
    }
}
