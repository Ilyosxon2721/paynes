<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        // Find user by login
        $user = User::where('login', $credentials['login'])->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Неверный логин или пароль.'],
            ]);
        }

        // Check if user is active
        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'login' => ['Ваш аккаунт неактивен. Обратитесь к администратору.'],
            ]);
        }

        // Create token for user
        $token = $user->createToken('api-token')->plainTextToken;

        // Log successful login
        Log::channel('auth')->info('User logged in', [
            'user_id' => $user->id,
            'login' => $user->login,
            'ip' => $request->ip(),
        ]);

        return ApiResponse::success([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Авторизация прошла успешно.');
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Log logout
        Log::channel('auth')->info('User logged out', [
            'user_id' => $request->user()->id,
            'login' => $request->user()->login,
            'ip' => $request->ip(),
        ]);

        // Delete the current access token
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Вы успешно вышли из системы.');
    }

    /**
     * Get the authenticated user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return ApiResponse::success(new UserResource($user));
    }
}
