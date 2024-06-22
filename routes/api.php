<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::group(['middleware' => ['auth:sanctum', 'ability:'.TokenAbility::ACCESS_TOKEN->value]], static function () {
    Route::get('logout', [AuthController::class, 'logOut']);
    Route::get('profile', [AuthController::class, 'profile']);
});

Route::get('refresh-token', [AuthController::class, 'refreshToken'])
    ->middleware(['auth:sanctum', 'ability:check-status,'.TokenAbility::REFRESH_TOKEN->value]);