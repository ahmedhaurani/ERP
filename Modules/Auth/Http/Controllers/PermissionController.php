<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Permission;

/**
 * @OA\Info(title="ERP API Documentation", version="1.0")
 * @OA\Tag(
 *     name="Permissions",
 *     description="API endpoints for managing permissions"
 * )
 */
class PermissionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/permissions",
     *     summary="List all permissions",
     *     tags={"Permissions"},
     *     @OA\Response(
     *         response=200,
     *         description="List of permissions",
     *         @OA\JsonContent(
     *             @OA\Property(property="permissions", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="edit users"),
     *                 @OA\Property(property="guard_name", type="string", example="api")
     *             ))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $permissions = Permission::all();
        return response()->json(['permissions' => $permissions]);
    }

    /**
     * @OA\Post(
     *     path="/api/permissions",
     *     summary="Create a new permission",
     *     tags={"Permissions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="edit users"),
     *             @OA\Property(property="guard_name", type="string", example="api")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission created successfully"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'guard_name' => 'nullable|string',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'api',
        ]);

        return response()->json([
            'message' => 'Permission created successfully',
            'data' => $permission
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/permissions/{id}",
     *     summary="Update a permission",
     *     tags={"Permissions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="edit articles"),
     *             @OA\Property(property="guard_name", type="string", example="api")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission updated successfully"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

        $permission->update([
            'name' => $request->name ?? $permission->name,
            'guard_name' => $request->guard_name ?? $permission->guard_name,
        ]);

        return response()->json(['message' => 'Permission updated successfully']);
    }

    /**
     * @OA\Delete(
     *     path="/api/permissions/{id}",
     *     summary="Delete a permission",
     *     tags={"Permissions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission deleted successfully"
     *     )
     * )
     */
    public function destroy($id)
    {
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

        $permission->delete();
        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
