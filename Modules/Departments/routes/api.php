<?php

//use Modules\Departments\Http\Controllers\DepartmentsController;



use Illuminate\Support\Facades\Route;
use Modules\Departments\Http\Controllers\Api\DepartmentController;

// Route::prefix('departments')->middleware('jwt.auth')->group(function () {
//     Route::get('/', [DepartmentController::class, 'index']);
//     Route::post('/', [DepartmentController::class, 'store']);
//     Route::get('{id}', [DepartmentController::class, 'show']);
//     Route::put('{id}', [DepartmentController::class, 'update']);
//     Route::delete('{id}', [DepartmentController::class, 'destroy']);
// });

Route::get('/departments', [DepartmentController::class, 'index']);


Route::middleware(['is_admin'])->group(function () {
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::put('/departments/{id}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{id}', [DepartmentController::class, 'destroy']);
});
