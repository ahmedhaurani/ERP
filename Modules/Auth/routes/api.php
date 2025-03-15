<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\PermissionController;
use Modules\Auth\Http\Controllers\RoleController;
use Modules\Auth\Http\Controllers\UserController;


// Route::get('/permissions', [PermissionController::class, 'index']);
// Route::post('/permissions', [PermissionController::class, 'store']);
// Route::put('/permissions/{id}', [PermissionController::class, 'update']);
// Route::delete('/permissions/{id}', [PermissionController::class, 'destroy']);

// Route::get('/roles', [RoleController::class, 'index']);
// Route::post('/roles', [RoleController::class, 'store']);
// Route::put('/roles/{role_id}', [RoleController::class, 'update']);
// Route::delete('/roles/{role_id}', [RoleController::class, 'destroy']);
// Route::post('/roles/{role_id}/permissions', [RoleController::class, 'assignPermissions']);
// //Route::post('/users/{user_id}/roles', [UserController::class, 'assignRoles']);




// Route::prefix('users')->group(function () {
//     Route::get('/', [UserController::class, 'index']);
//     Route::post('/', [UserController::class, 'store']);
//     Route::put('{id}', [UserController::class, 'update']);
//     Route::delete('{id}', [UserController::class, 'destroy']);
//     Route::post('{user_id}/roles', [UserController::class, 'assignRoles']);
//     Route::post('{user_id}/permissions', [UserController::class, 'assignPermissions']);
// });
// Route::prefix('auth')->group(function () {
//     Route::post('register', [AuthController::class, 'register']);
//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('logout', [AuthController::class, 'logout'])->middleware('jwt.auth');
//     Route::get('me', [AuthController::class, 'me'])->middleware('jwt.auth');
// });




// 🛡️ Routes اللي تحتاج تسجيل دخول
Route::middleware('jwt.auth')->group(function () {

    // Permissions Routes
    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::post('/permissions', [PermissionController::class, 'store']);
    Route::put('/permissions/{id}', [PermissionController::class, 'update']);
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy']);

    // Roles Routes
    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::put('/roles/{role_id}', [RoleController::class, 'update']);
    Route::delete('/roles/{role_id}', [RoleController::class, 'destroy']);
    Route::post('/roles/{role_id}/permissions', [RoleController::class, 'assignPermissions']);

    // Users Routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('{id}', [UserController::class, 'update']);
        Route::delete('{id}', [UserController::class, 'destroy']);
        Route::post('{user_id}/roles', [UserController::class, 'assignRoles']);
        Route::post('{user_id}/permissions', [UserController::class, 'assignPermissions']);
    });

    // Auth Protected Routes
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);
});

// Routes المتاحة بدون تسجيل دخول
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});
