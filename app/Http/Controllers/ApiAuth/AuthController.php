<?php

namespace App\Http\Controllers\ApiAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiAuth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuthController extends Controller
{
    /**
     * 1.1 Registration
     *
     * @param  RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create(
                [
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
                ]
            );

            $token = $user->createToken(env('APP_NAME'))->accessToken;

            return response()->json(['token' => $token]);
        } catch (Throwable $throwable) {
            Log::debug('Register error: ' . $throwable->getMessage());

            return response()->json([$throwable->getMessage()], 400);
        }

        return response()->json([], 400);
    }


    /**
     * 1.2 Login
     *
     * @param  LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = [
                'email' => $request->get('email'),
                'password' => $request->get('password')
            ];

            if (auth()->attempt($data)) {
                $token = auth()->user()->createToken(env('APP_NAME'))->accessToken;

                return response()->json(['token' => $token]);
            }
        } catch (Throwable $throwable) {
            Log::debug('Login error: ' . $throwable->getMessage());
        }

        return response()->json(['error' => 'Unauthorised'], 401);
    }

    /**
     * 1.3 Logout
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            $token = request()->user()->token();

            $token->revoke();

            return response()->json(['success' => true]);
        } catch (Throwable $throwable) {
            Log::debug('Logout error: ' . $throwable->getMessage());
        }

        return response()->json(['success' => false], 400);
    }
}
