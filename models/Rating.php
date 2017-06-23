<?php

namespace app\models;

use app\components\Enum;

class Rating extends Enum
{
    static $_values = [
        "3 - 3" => "3 - 3",
        "5 - 4 - 4" => "5 - 4 - 4",
        "5 - 5" => "5 - 5",
    ];

    public static function getValue($key)
    {
        if (!isset(static::$_values[$key])) {
            return $key;
        }
        return static::$_values[$key];
    }
}