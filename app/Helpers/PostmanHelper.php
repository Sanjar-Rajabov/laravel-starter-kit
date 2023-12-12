<?php

namespace App\Helpers;

use App\Enums\HttpCode;
use App\Enums\HttpStatus;

class PostmanHelper
{
    public static function response(mixed $data, HttpCode $status = HttpCode::OK): array
    {
        return [
            'statusCode' => $status->value,
            'statusDescription' => HttpStatus::status($status),
            'data' => $data
        ];
    }

    public static function paginate(mixed $data = [], HttpCode $status = HttpCode::OK): array
    {
        return [
            'pagination' => [
                'current' => 1,
                'previous' => 0,
                'next' => 2,
                'perPage' => 20,
                'totalPage' => 10,
                'totalItem' => 200,
            ],
            'list' => $data
        ];
    }
}
