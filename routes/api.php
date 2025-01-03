<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// AUTH
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('login/otp', [AuthController::class, 'login_otp'])->name('otp.login');
Route::delete('logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('change-password', [AuthController::class, 'change_password'])->name('change-password');
});
