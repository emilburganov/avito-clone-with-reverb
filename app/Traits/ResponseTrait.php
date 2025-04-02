<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * @param $errors
     * @return JsonResponse
     */
    public function validationErrors($errors): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 422,
                'message' => 'Validation error',
                'errors' => $errors,
            ]
        ], 422);
    }

    /**
     * @return JsonResponse
     */
    public function unauthorizedError(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 403,
                'message' => 'Login failed',
            ]
        ], 403);
    }

    /**
     * @return JsonResponse
     */
    public function forbiddenError(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 403,
                'message' => 'Forbidden for you',
            ]
        ], 403);
    }
}
