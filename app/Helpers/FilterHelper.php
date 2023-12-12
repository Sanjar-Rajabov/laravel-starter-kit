<?php

namespace App\Helpers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class FilterHelper
{
    public static function whereEqual(Builder $query, $column, $value): Builder
    {
        return $query->where($column, '=', $value);
    }

    public static function whereLike(Builder $query, $column, $value): Builder
    {
        return $query->where(DB::raw("lower($column)"), 'LIKE', '%' . mb_strtolower($value) . '%');
    }

    public static function whereIn(Builder $query, $column, $value): Builder
    {
        return $query->whereIn($column, (array)$value);
    }

    public static function whereBetween(Builder $query, $column, $value): Builder
    {
        return $query->whereBetween($column, (array)$value);
    }

    public static function whereDate(Builder $query, $column, $value): Builder
    {
        return $query->where($column, '=', date('Y-m-d', strtotime($value)));
    }

    public static function whereDatetime(Builder $query, $column, $value): Builder
    {
        return $query->where($column, '=', date('Y-m-d H:i:s', strtotime($value)));
    }

    public static function whereLocalized(Builder $query, $column, $value): Builder
    {
        return $query->where(
            fn(Builder $query) => $query->where(DB::raw("lower({$column}->>'ru')"), 'LIKE', '%' . mb_strtolower($value) . '%')
                ->orWhere(DB::raw("lower({$column}->>'uz')"), 'LIKE', '%' . mb_strtolower($value) . '%')
                ->orWhere(DB::raw("lower({$column}->>'en')"), 'LIKE', '%' . mb_strtolower($value) . '%')
        );
    }
}
