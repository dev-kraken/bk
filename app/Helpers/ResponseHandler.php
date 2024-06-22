<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHandler
{
    public static function success($message = 'Request successfully completed', $data = null, $code = 200): JsonResponse
    {
        $response = [
            'status' => 'success',
            'message' => $message,
        ];
        if (!is_null($data)) {
            $response['data'] = $data;
        }
        return response()->json($response, $code);
    }

    public static function error($message, $code = 400, $data = null): JsonResponse
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];
        if (!is_null($data)) {
            $response['data'] = $data;
        }
        return response()->json($response, $code);
    }
}
