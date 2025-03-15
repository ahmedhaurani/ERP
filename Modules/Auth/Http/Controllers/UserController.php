<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints for Managing Users"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="List all users",
     *     tags={"Users"},
     *     @OA\Response(response=200, description="List of users")
     * )
     */
    public function index()
    {
        if (!auth()->user() || !auth()->user()->can('view users')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(User::with('roles', 'permissions')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="Ali Ahmed"),
     *             @OA\Property(property="email", type="string", example="ali@example.com"),
     *             @OA\Property(property="password", type="string", example="123456"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="department_id", type="integer", example=1),
     *             @OA\Property(property="is_admin", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(response=200, description="User created successfully")
     * )
     */
    public function store(Request $request)
    {
        if (!auth()->user() || !auth()->user()->can('create users')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'roles' => 'nullable|array',
            'roles.*' => 'string',
            'department_id' => 'nullable|integer',
            'is_admin' => 'nullable|boolean'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'is_admin' => $request->is_admin ?? 0,
        ]);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return response()->json(['message' => 'User created successfully', 'user' => $user]);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update a user",
     *     tags={"Users"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="department_id", type="integer"),
     *             @OA\Property(property="is_admin", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user() || !auth()->user()->can('edit users')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);

        $data = $request->only(['name', 'email', 'department_id', 'is_admin']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return response()->json(['message' => 'User updated successfully']);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="User deleted successfully")
     * )
     */
    public function destroy($id)
    {
        if (!auth()->user() || !auth()->user()->can('delete users')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{user_id}/roles",
     *     summary="Assign roles to a user",
     *     tags={"Users"},
     *     @OA\Parameter(name="user_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="roles", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Roles assigned successfully")
     * )
     */
    public function assignRoles(Request $request, $userId)
    {
        if (!auth()->user() || !auth()->user()->can('assign roles')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'string'
        ]);

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->syncRoles($request->roles);
        return response()->json(['message' => 'Roles assigned successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{user_id}/permissions",
     *     summary="Assign permissions to a user",
     *     tags={"Users"},
     *     @OA\Parameter(name="user_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="permissions", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Permissions assigned successfully")
     * )
     */
    public function assignPermissions(Request $request, $userId)
    {
        if (!auth()->user() || !auth()->user()->can('assign permissions')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string'
        ]);

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->syncPermissions($request->permissions);
        return response()->json(['message' => 'Permissions assigned successfully']);
    }
}
