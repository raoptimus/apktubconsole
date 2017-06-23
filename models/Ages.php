<?php

namespace app\models;

use app\components\Enum;

class Ages extends Enum
{
    static $_values = [
        "18" => "18+",
        "12" => "12+",
        "6" => "6+",
        "0" => "0+",
    ];

    public static function getValue($key)
    {
        if (!isset(static::$_values[$key])) {
            return $key;
        }
        return static::$_values[$key];
    }
}