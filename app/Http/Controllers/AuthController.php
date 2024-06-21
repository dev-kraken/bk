<?php

namespace App\Http\Controllers;

use App\Enums\TokenAbility;
use App\Facades\AuthServiceFacade;
use App\Helpers\ResponseHandler;
use App\Http\Requests\Auth\ApiLoginRequest;
use App\Http\Requests\Auth\ApiRegisterRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function register(ApiRegisterRequest $request): JsonResponse
    {
        AuthServiceFacade::register($request->validated());
        return response()->json([
            'message' => 'User created successfully',
            'status' => true
        ], 201);
    }

    /**
     * @throws ValidationException
     */
    public function login(ApiLoginRequest $request): JsonResponse
    {
        $data = collect($request->validated());

        if (!Auth::attempt($data->only(['email', 'password'])->all())) {
            return ResponseHandler::error('The provided credentials are incorrect.', 401);
        }

        $user = $request->user();

        $user->tokens()->delete();

        // Create Access Token with a custom expiration
        $accessTokenExpiration = Carbon::now()->addMinutes(15);
        $user['access_token'] = $user->createToken('access-token', [TokenAbility::ACCESS_TOKEN->value],
            $accessTokenExpiration)->plainTextToken;

        // Create Refresh Token with a custom expiration
        $refreshTokenExpiration = Carbon::now()->addDays(7);
        $user['refresh_token'] = $user->createToken('refresh-token', [TokenAbility::REFRESH_TOKEN->value],
            $refreshTokenExpiration)->plainTextToken;

        return ResponseHandler::success($user, 'User logged in successfully');
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->where('name', 'access-token')->delete();

        $accessTokenExpiration = Carbon::now()->addMinutes(15);
        $accessToken = $user->createToken('access-token',
            [TokenAbility::ACCESS_TOKEN->value], $accessTokenExpiration)->plainTextToken;

        return ResponseHandler::success([
            'access_token' => $accessToken,
            'access_token_expiration' => $accessTokenExpiration,
        ], 'Access token refreshed successfully');
    }

    public function logOut(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return ResponseHandler::error('Unable to logout due to some issue.', 401);
        }
        $user->currentAccessToken()->delete();
        return ResponseHandler::success('Logged out successfully');
    }


    public function profile(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHandler::error('Unable to process request', 401);
        }

        return ResponseHandler::success($user, 'User Profile Data');
    }
}
