<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API without login
Route::group(['prefix' => 'auth'], function () {
    // login
    Route::post('login', [AuthController::class, 'login']);
    // register
    Route::post('register', [AuthController::class, 'register']);
    // verif and resend email
    Route::get('email/verify/{id}', [AuthController::class, 'verifyEmail'])->name('verification.verify'); // Make sure to keep this as your route name
    Route::get('email/resend', [AuthController::class, 'resendEmailVerification'])->name('verification.resend');
    // forgot password
    Route::post('forgotPassword', [AuthController::class, 'sendEmailForgotPassword'])->middleware('guest')->name('password.email');
    // reset password
    Route::post('resetPassword', [AuthController::class, 'resetPassword'])->middleware('guest')->name('password.reset');
});

// logout
Route::group(['prefix' => 'auth', 'middleware' => ['auth:sanctum', 'verified']], function () {
    //logout
    Route::get('logout', [AuthController::class, 'logout']);
    // change password
    Route::put('changePassword', [AuthController::class, 'changePassword']);
});
