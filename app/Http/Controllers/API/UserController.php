<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::with('roles', 'permissions');

            // Filter by position
            if ($request->has('position')) {
                $query->where('position', $request->position);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by branch
            if ($request->has('branch')) {
                $query->where('branch', $request->branch);
            }

            $users = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'login' => $user->login,
                        'full_name' => $user->full_name,
                        'position' => $user->position,
                        'status' => $user->status,
                        'branch' => $user->branch,
                        'reward_percentage' => $user->reward_percentage,
                        'roles' => $user->roles->pluck('name'),
                        'permissions' => $user->permissions->pluck('name'),
                        'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка сотрудников: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'login' => 'required|string|unique:users,login|max:255',
                'full_name' => 'required|string|max:255',
                'password' => 'required|string|min:6',
                'position' => ['required', Rule::in(['admin', 'cashier'])],
                'status' => ['required', Rule::in(['active', 'blocked'])],
                'branch' => 'nullable|string|max:255',
                'reward_percentage' => 'nullable|numeric|min:0|max:100',
                'roles' => 'nullable|array',
                'roles.*' => 'exists:roles,name',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            $user = User::create([
                'login' => $validated['login'],
                'full_name' => $validated['full_name'],
                'password' => $validated['password'],
                'position' => $validated['position'],
                'status' => $validated['status'],
                'branch' => $validated['branch'] ?? null,
                'reward_percentage' => $validated['reward_percentage'] ?? 0,
            ]);

            // Assign roles
            if (!empty($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            } else {
                // Auto-assign role based on position
                $user->syncRoles([$validated['position']]);
            }

            // Assign permissions
            if (!empty($validated['permissions'])) {
                $user->syncPermissions($validated['permissions']);
            }

            $user->load('roles', 'permissions');

            return response()->json([
                'success' => true,
                'message' => 'Сотрудник успешно создан',
                'data' => [
                    'id' => $user->id,
                    'login' => $user->login,
                    'full_name' => $user->full_name,
                    'position' => $user->position,
                    'status' => $user->status,
                    'branch' => $user->branch,
                    'reward_percentage' => $user->reward_percentage,
                    'roles' => $user->roles->pluck('name'),
                    'permissions' => $user->permissions->pluck('name'),
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании сотрудника: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified user.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::with('roles', 'permissions')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'login' => $user->login,
                    'full_name' => $user->full_name,
                    'position' => $user->position,
                    'status' => $user->status,
                    'branch' => $user->branch,
                    'reward_percentage' => $user->reward_percentage,
                    'roles' => $user->roles->pluck('name'),
                    'permissions' => $user->permissions->pluck('name'),
                    'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Сотрудник не найден',
            ], 404);
        }
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'login' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
                'full_name' => 'required|string|max:255',
                'password' => 'nullable|string|min:6',
                'position' => ['required', Rule::in(['admin', 'cashier'])],
                'status' => ['required', Rule::in(['active', 'blocked'])],
                'branch' => 'nullable|string|max:255',
                'reward_percentage' => 'nullable|numeric|min:0|max:100',
                'roles' => 'nullable|array',
                'roles.*' => 'exists:roles,name',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,name',
            ]);

            $updateData = [
                'login' => $validated['login'],
                'full_name' => $validated['full_name'],
                'position' => $validated['position'],
                'status' => $validated['status'],
                'branch' => $validated['branch'] ?? null,
                'reward_percentage' => $validated['reward_percentage'] ?? 0,
            ];

            // Only update password if provided
            if (!empty($validated['password'])) {
                $updateData['password'] = $validated['password'];
            }

            $user->update($updateData);

            // Update roles
            if (isset($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            }

            // Update permissions
            if (isset($validated['permissions'])) {
                $user->syncPermissions($validated['permissions']);
            }

            $user->load('roles', 'permissions');

            return response()->json([
                'success' => true,
                'message' => 'Сотрудник успешно обновлен',
                'data' => [
                    'id' => $user->id,
                    'login' => $user->login,
                    'full_name' => $user->full_name,
                    'position' => $user->position,
                    'status' => $user->status,
                    'branch' => $user->branch,
                    'reward_percentage' => $user->reward_percentage,
                    'roles' => $user->roles->pluck('name'),
                    'permissions' => $user->permissions->pluck('name'),
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении сотрудника: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting the currently authenticated user
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы не можете удалить свой собственный аккаунт',
                ], 400);
            }

            // Check if user has associated records
            $hasPayments = $user->payments()->exists();
            $hasExchanges = $user->exchanges()->exists();
            $hasCredits = $user->credits()->exists();

            if ($hasPayments || $hasExchanges || $hasCredits) {
                return response()->json([
                    'success' => false,
                    'message' => 'Невозможно удалить сотрудника, так как у него есть связанные операции',
                ], 400);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Сотрудник успешно удален',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении сотрудника: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all available roles.
     */
    public function getRoles(): JsonResponse
    {
        try {
            $roles = Role::all(['id', 'name']);

            return response()->json([
                'success' => true,
                'data' => $roles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении ролей: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all available permissions.
     */
    public function getPermissions(): JsonResponse
    {
        try {
            $permissions = Permission::all(['id', 'name']);

            return response()->json([
                'success' => true,
                'data' => $permissions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении привилегий: ' . $e->getMessage(),
            ], 500);
        }
    }
}
