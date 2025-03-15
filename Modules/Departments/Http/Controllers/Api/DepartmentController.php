<?php

namespace Modules\Departments\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;

/**
 * @OA\Tag(
 *     name="Departments",
 *     description="Endpoints لإدارة الأقسام"
 * )
 */

class DepartmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/departments",
     *     summary="جلب الأقسام",
     *     tags={"Departments"},
     *     @OA\Response(
     *         response=200,
     *         description="قائمة الأقسام"
     *     )
     * )
     */
    public function index()
    {
        return Department::with('children')->whereNull('parent_id')->get();
    }

    /**
     * @OA\Post(
     *     path="/api/departments",
     *     tags={"Departments"},
     *     summary="إضافة قسم جديد (فقط للإدمن)",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="قسم الاتصالات"),
     *             @OA\Property(property="parent_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="تم إنشاء القسم"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="صلاحيات غير كافية"
     *     )
     * )
     */
    public function store(Request $request)
    {
        // تحقق من صلاحيات admin
        if (auth()->user()->is_admin !== true) {
            return response()->json(['message' => 'ليس لديك صلاحيات لإضافة قسم'], 403);
        }

        $request->validate([
            'name' => 'required|string',
            'parent_id' => 'nullable|exists:departments,id'
        ]);

        $department = Department::create($request->only('name', 'parent_id'));

        return response()->json(['message' => 'تم إنشاء القسم بنجاح', 'department' => $department], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/departments/{id}",
     *     tags={"Departments"},
     *     summary="تحديث قسم (فقط للإدمن)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="قسم الشبكات"),
     *             @OA\Property(property="parent_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم التحديث بنجاح"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="صلاحيات غير كافية"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        // تحقق من صلاحيات admin
        if (auth()->user()->is_admin !== true) {
            return response()->json(['message' => 'ليس لديك صلاحيات لتحديث هذا القسم'], 403);
        }

        $department = Department::findOrFail($id);
        $department->update($request->only('name', 'parent_id'));

        return response()->json(['message' => 'تم التحديث بنجاح']);
    }

    /**
     * @OA\Delete(
     *     path="/api/departments/{id}",
     *     tags={"Departments"},
     *     summary="حذف قسم (فقط للإدمن)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="تم حذف القسم"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="صلاحيات غير كافية"
     *     )
     * )
     */
    public function destroy($id)
    {
        // تحقق من صلاحيات admin
        if (auth()->user()->is_admin !== true) {
            return response()->json(['message' => 'ليس لديك صلاحيات لحذف هذا القسم'], 403);
        }

        Department::destroy($id);
        return response()->json(['message' => 'تم حذف القسم']);
    }
}
