<?php

namespace App\Postman;

use ReflectionException;

class Postman
{

    public static string $baseUrlVariable = '{{baseUrl}}';

    /**
     * @throws ReflectionException
     */
    public static function generate(): array
    {
        return [
            'info' => self::info(),
            'item' => self::item(),
            'variable' => self::variable()
        ];
    }

    /**
     * @throws ReflectionException
     */
    protected static function item(): array
    {
        return PostmanRoute::generate();
    }


    protected static function variable(): array
    {
        return PostmanVariable::generate();
    }

    protected static function info(): array
    {
        return [
            'name' => env('APP_NAME'),
            'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
        ];
    }
}
