<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class RouteHelper
{
    private static array $methods = [
        'getAll' => [
            'method' => 'get',
            'url' => ''
        ],
        'getOne' => [
            'method' => 'get',
            'url' => '{id}'
        ],
        'create' => [
            'method' => 'post',
            'url' => ''
        ],
        'update' => [
            'method' => 'post',
            'url' => '{id}'
        ],
        'delete' => [
            'method' => 'delete',
            'url' => '{id}'
        ]
    ];

    public static function resource(string $prefix, string $controller, array $only = []): void
    {
        $methods = self::$methods;

        if (!empty($only)) {
            foreach ($methods as $key => $method) {
                if (!in_array($key, $only)) {
                    unset($methods[$key]);
                }
            }
        }

        Route::prefix($prefix)->controller($controller)->group(function () use ($controller, $methods) {
            foreach ($methods as $method => $options) {
                Route::{$options['method']}($options['url'], [$controller, $method]);
            }
        });
    }
}
