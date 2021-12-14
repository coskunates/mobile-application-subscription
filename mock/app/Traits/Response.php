<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait Response
 * @package App\Traits
 */
trait Response
{
    /**
     * Building success response
     *
     * @param array $data
     * @param int $code
     * @param string $message
     *
     * @return JsonResponse
     */
    public function successResponse(array $data, int $code, string $message): JsonResponse
    {
        return new JsonResponse([
            'code' => $code,
            'type' => 'success',
            'error' => false,
            'message' => $message,
            'data' => empty($data) ? null : $data
        ], $code);
    }

    /**
     * @param string $message
     * @param int $code
     * @param string $type
     *
     * @return JsonResponse
     */
    public function errorResponse(string $message, int $code, string $type = 'danger'): JsonResponse
    {
        return new JsonResponse([
            'code' => $code,
            'type' => $type,
            'error' => true,
            'message' => $message
        ], $code);
    }
}
