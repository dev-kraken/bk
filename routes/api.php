<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\AuthController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware(Authenticate::using('sanctum', 'ability:'.TokenAbility::ACCESS_TOKEN->value))->group(function () {
    Route::get('logout', [AuthController::class, 'logOut']);
    Route::get('profile', [AuthController::class, 'profile']);
});

Route::get('refresh-token', [AuthController::class, 'refreshToken'])
    ->middleware(Authenticate::using('sanctum', 'ability:'.TokenAbility::REFRESH_TOKEN->value));