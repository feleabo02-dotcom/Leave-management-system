<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DatabaseHelper
{
    public static function month(string $column, string $alias = 'month'): string
    {
        $driver = DB::getDriverName();
        return match ($driver) {
            'mysql' => "DATE_FORMAT($column, '%m') as $alias",
            'pgsql' => "TO_CHAR($column, 'MM') as $alias",
            default => "strftime('%m', $column) as $alias",
        };
    }

    public static function year(string $column, string $alias = 'year'): string
    {
        $driver = DB::getDriverName();
        return match ($driver) {
            'mysql' => "DATE_FORMAT($column, '%Y') as $alias",
            'pgsql' => "TO_CHAR($column, 'YYYY') as $alias",
            default => "strftime('%Y', $column) as $alias",
        };
    }

    public static function dayOfWeek(string $column, string $alias = 'day'): string
    {
        $driver = DB::getDriverName();
        return match ($driver) {
            'mysql' => "(DAYOFWEEK($column) - 1) as $alias",
            'pgsql' => "EXTRACT(DOW FROM $column)::int as $alias",
            default => "strftime('%w', $column) as $alias",
        };
    }

    public static function yearMonth(string $column, string $alias = 'year_month'): string
    {
        $driver = DB::getDriverName();
        return match ($driver) {
            'mysql' => "DATE_FORMAT($column, '%Y-%m') as $alias",
            'pgsql' => "TO_CHAR($column, 'YYYY-MM') as $alias",
            default => "strftime('%Y-%m', $column) as $alias",
        };
    }
}
