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
     * Handle a registration request.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tariff' => 'required|string|in:starter,business,enterprise',
            'name' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users,login',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'branch' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'tariff.required' => 'Выберите тариф.',
            'tariff.in' => 'Некорректный тариф.',
            'login.unique' => 'Этот логин уже занят.',
            'email.unique' => 'Этот email уже зарегистрирован.',
            'password.confirmed' => 'Пароли не совпадают.',
            'password.min' => 'Пароль должен быть минимум 6 символов.',
        ]);

        $tariffNames = [
            'starter' => 'Стартовый (299 000 UZS/мес)',
            'business' => 'Бизнес (599 000 UZS/мес)',
            'enterprise' => 'Корпоративный (1 199 000 UZS/мес)',
        ];

        $user = User::create([
            'name' => $validated['name'],
            'login' => $validated['login'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'branch' => $validated['branch'] ?? null,
            'password' => $validated['password'],
            'position' => 'agent',
            'status' => 'inactive',
            'tariff' => $validated['tariff'],
        ]);

        // Assign agent role if exists
        try {
            $user->assignRole('cashier');
        } catch (\Exception $e) {
            // Role might not exist
        }

        Log::channel('auth')->info('New agent registered', [
            'user_id' => $user->id,
            'login' => $user->login,
            'email' => $user->email,
            'tariff' => $validated['tariff'],
            'ip' => $request->ip(),
        ]);

        // Send email notification to admin
        try {
            $adminEmail = config('mail.admin_email', 'admin@paynes.uz');
            \Illuminate\Support\Facades\Mail::raw(
                "Новая регистрация агента!\n\n" .
                "Имя: {$user->name}\n" .
                "Логин: {$user->login}\n" .
                "Email: {$user->email}\n" .
                "Телефон: " . ($user->phone ?: 'Не указан') . "\n" .
                "Филиал: " . ($user->branch ?: 'Не указан') . "\n" .
                "Тариф: " . $tariffNames[$validated['tariff']] . "\n\n" .
                "Для активации аккаунта перейдите в админ-панель.",
                function ($message) use ($adminEmail, $user) {
                    $message->to($adminEmail)
                            ->subject('Новая регистрация: ' . $user->name);
                }
            );
        } catch (\Exception $e) {
            Log::warning('Failed to send admin notification email', ['error' => $e->getMessage()]);
        }

        return ApiResponse::success([
            'user' => new UserResource($user),
        ], 'Регистрация успешна! Ваш аккаунт будет активирован после проверки администратором.');
    }

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
