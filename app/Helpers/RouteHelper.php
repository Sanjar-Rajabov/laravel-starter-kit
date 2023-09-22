<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class RouteHelper
{
    private static array $methods = [
        'index' => [
            'method' => 'get',
            'url' => ''
        ],
        'show' => [
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

    public static function resource(string $prefix, string $controller, array $except = []): void
    {
        $methods = self::$methods;

        if (isset($options['except'])) {
            $methods = array_diff($methods, (array) $options['except']);
        }

        Route::prefix($prefix)->controller($controller)->group(function () use ($controller, $methods) {
            foreach ($methods as $method => $options) {
                Route::{$options['method']}($options['url'], [$controller, $method]);
            }
        });
    }
}
