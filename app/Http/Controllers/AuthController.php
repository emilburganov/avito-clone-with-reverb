<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ResponseTrait;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $data = $validator->safe();

        User::query()->create($data->only('email', 'password'));

        return response()->json([
            'data' => [
                'message' => 'Успех',
            ],
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = validator($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrors($validator->errors());
        }

        $data = $validator->safe();

        if (!auth()->attempt($data->only('email', 'password'))) {
            return response()->json([
                'error' => [
                    'code' => 401,
                    'message' => 'Authentication failed',
                ],
            ], 401);
        }

        $token = Str::uuid();

        $user = auth()->user();
        $user->update([
            'token' => $token,
        ]);

        return response()->json([
            'data' => [
                'user_token' => $token,
            ]
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $user = auth()->user();
        $user->update(['token' => null]);

        return response()->json([
            'data' => [
                'message' => 'logout',
            ]
        ]);
    }
}
