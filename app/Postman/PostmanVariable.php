<?php

namespace App\Postman;

class PostmanVariable
{
    public static function generate(): array
    {
        return [
            [
                'key' => 'baseUrl',
                'value' => env('APP_URL'),
                'type' => 'string'
            ]
        ];
    }
}
