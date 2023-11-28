<?php

namespace App\Helpers;

use App\Enums\HttpCode;
use App\Enums\HttpStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function response(mixed $data, HttpCode $status = HttpCode::OK): JsonResponse
    {
        return response()->json([
            'statusCode' => $status->value,
            'statusDescription' => HttpStatus::status($status),
            'data' => $data
        ], $status->value);
    }

    public static function model(Model $model, string $resource = null): JsonResponse
    {
        if (!empty($resource)) {
            $data = (new $resource($model));
        } else {
            $data = $model;
        }

        return self::response($data, HttpCode::OK);
    }

    public static function items(Collection|LengthAwarePaginator $items, string $resource = null): JsonResponse
    {
        if (!empty($resource)) {
            $data = call_user_func([$resource, 'collection'], $items);
        } else {
            $data = $items;
        }

        if ($items instanceof LengthAwarePaginator) {
            return self::response([
                'pagination' => [
                    'current' => $items->currentPage(),
                    'previous' => $items->currentPage() > 1 ? $items->currentPage() - 1 : 0,
                    'next' => $items->hasMorePages() ? $items->currentPage() + 1 : 0,
                    'perPage' => $items->perPage(),
                    'totalPage' => $items->lastPage(),
                    'totalItem' => $items->total(),
                ],
                'list' => $items->items()
            ], HttpCode::OK);
        } else {
            return self::response($data, HttpCode::OK);
        }
    }

    public static function created(): JsonResponse
    {
        return self::response(null, HttpCode::CREATED);
    }

    public static function updated(): JsonResponse
    {
        return self::response(null, HttpCode::OK);
    }

    public static function accepted(): JsonResponse
    {
        return self::response(null, HttpCode::ACCEPTED);
    }

    public static function deleted(): JsonResponse
    {
        return self::response(null, HttpCode::NO_CONTENT);
    }
}
