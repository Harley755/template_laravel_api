<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AppConfigurationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// AUTH
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('login/otp', [AuthController::class, 'login_otp'])->name('otp.login');
Route::delete('logout', [AuthController::class, 'logout'])->name('logout');

// App Configurations
Route::get('app-configurations', [AppConfigurationController::class, 'index'])->name('app-configurations.index');
Route::get('app-configurations/{app_configuration}', [AppConfigurationController::class, 'show'])->name('app-configurations.show');
Route::post('app-configurations/search', [AppConfigurationController::class, 'search'])->name('app-configurations.search');

Route::group(['middleware' => 'auth:sanctum'], function () {
    // User
    Route::post('users/search', [UserController::class, 'search'])->name('users.search');
    Route::apiResource('users', UserController::class);

    // Roles
    Route::apiResource('roles', RoleController::class);
    Route::post('roles/search', [RoleController::class, 'search'])->name('roles.search');
    Route::post('roles/permissions/manage', [RoleController::class, 'permission_manage'])->name('roles.permissions.manage');

    // Permission
    Route::apiResource('permissions', PermissionController::class);
    Route::post('permissions/search', [PermissionController::class, 'search'])->name('permissions.search');

    // App Configurations
    Route::apiResource('app-configurations', AppConfigurationController::class)->except(['index']);

    // Change Password
    Route::post('change-password', [AuthController::class, 'change_password'])->name('change-password');
});
