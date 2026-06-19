<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Issue a personal access token for valid credentials.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->credentials();

            /** @var User|null $user */
            $user = User::query()->where('email', $credentials['email'])->first();

            if ($user === null || ! Hash::check($credentials['password'], $user->password)) {
                return $this->respondError('The provided credentials are incorrect.', 401);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return $this->respondError('Unable to sign in right now.', 500);
        }
    }

    /**
     * Revoke the access token used to authenticate the current request.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $token = $request->user()?->currentAccessToken();

            if ($token !== null) {
                $token->delete();
            }

            return $this->respondNoContent();
        } catch (\Throwable $exception) {
            report($exception);

            return $this->respondError('Unable to sign out right now.', 500);
        }
    }
}
