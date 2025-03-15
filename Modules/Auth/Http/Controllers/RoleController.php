<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="Get all roles",
     *     tags={"Roles"},
     *     @OA\Response(
     *         response=200,
     *         description="List of roles",
     *         @OA\JsonContent(
     *             @OA\Property(property="roles", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="admin"),
     *                 @OA\Property(property="guard_name", type="string", example="api")
     *             ))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json(['roles' => $roles]);
    }

    /**
     * @OA\Post(
     *     path="/api/roles",
     *     summary="Create a new role",
     *     description="Create a new role with optional guard name",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="admin"),
     *             @OA\Property(property="guard_name", type="string", example="api")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Role created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="admin"),
     *                 @OA\Property(property="guard_name", type="string", example="api"),
     *                 @OA\Property(property="created_at", type="string", example="2025-03-15T09:11:27.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2025-03-15T09:11:27.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'guard_name' => 'nullable|string',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'api',
        ]);

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/roles/{role_id}",
     *     summary="Update a role",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="role_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="moderator"),
     *             @OA\Property(property="guard_name", type="string", example="api")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role updated successfully"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) return response()->json(['message' => 'Role not found'], 404);

        $role->update([
            'name' => $request->name ?? $role->name,
            'guard_name' => $request->guard_name ?? $role->guard_name,
        ]);

        return response()->json(['message' => 'Role updated successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/roles/{role_id}/permissions",
     *     summary="Assign permissions to a role",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="role_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"edit users", "delete users"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permissions assigned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Permissions assigned successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found"
     *     )
     * )
     */
    public function assignPermissions(Request $request, $roleId)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string'
        ]);

        $role = Role::find($roleId);
        if (!$role) return response()->json(['message' => 'Role not found'], 404);

        $role->syncPermissions($request->permissions);
        return response()->json(['message' => 'Permissions assigned successfully']);
    }

    /**
     * @OA\Delete(
     *     path="/api/roles/{role_id}",
     *     summary="Delete a role",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="role_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role deleted successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) return response()->json(['message' => 'Role not found'], 404);

        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
