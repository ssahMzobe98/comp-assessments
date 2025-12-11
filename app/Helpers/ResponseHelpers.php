<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseHelpers
{
    public static function successResponse($data = [],$meta = [], string $message = 'Success'):JsonResponse{
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta
        ], Response::HTTP_OK);
    }
    public static function errorResponse($message = 'Error detected!!', $code = Response::HTTP_BAD_REQUEST):JsonResponse{
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => []
        ], $code);
    }

}