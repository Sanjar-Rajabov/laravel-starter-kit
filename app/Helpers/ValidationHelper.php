<?php

namespace App\Helpers;

class ValidationHelper
{
    public static function localized(string $column): array
    {
        return [
            $column => 'required|array',
            $column . '.ru' => 'required|string',
            $column . '.uz' => 'required|string',
            $column . '.en' => 'required|string'
        ];
    }
}
