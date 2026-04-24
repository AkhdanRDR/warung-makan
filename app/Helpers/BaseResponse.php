<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseResponse
{
    public static function Success(
        string $message = 'Request successful',
        mixed $data = [],
        int $statusCode = 200,
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public static function Error(
        string $message = 'Request failed',
        mixed $data = [],
        int $statusCode = 400,
    ): JsonResponse {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    public static function Paginate(
        string $message = 'Request successful',
        mixed $data = [],
        int $statusCode = 200,
        LengthAwarePaginator $collection
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data->items(),
            'pagination' => self::PaginateHelper($collection)
        ], $statusCode);
    }

    private static function PaginateHelper(
        LengthAwarePaginator $collection
    ): array {
        return [
            'total' => $collection->total(),
            'per_page' => $collection->perPage(),
            'current_page' => $collection->currentPage(),
            'last_page' => $collection->lastPage(),
            'from' => $collection->firstItem(),
            'to' => $collection->lastItem(),
        ];
    }
}
