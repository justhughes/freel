<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'role' => User::ROLE_CLIENT,
            'account_status' => User::STATUS_ACTIVE,
        ]);

        $token = $user->createToken('contify-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil.',
            'token' => $token,
            'user' => new UserResource($user),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        if (!Auth::attempt([
            $field => $request->login,
            'password' => $request->password,
        ])) {

            return response()->json([
                'success' => false,
                'message' => 'Username/email atau password salah.'
            ], 401);
        }

        $user = Auth::user();

        if (!$user->isActive()) {

            Auth::logout();

            return response()->json([
                'success' => false,
                'message' => match ($user->account_status) {
                    User::STATUS_PENDING => 'Akun masih menunggu persetujuan admin.',
                    User::STATUS_REJECTED => 'Pendaftaran akun ditolak.',
                    default => 'Akun dinonaktifkan.',
                }
            ], 403);
        }

        $user->tokens()->delete();

        $token = $user->createToken('contify-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => new UserResource($request->user()),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }
}