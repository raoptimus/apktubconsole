<?php

namespace app\models;

use app\components\Enum;

class Currency extends Enum
{
    static $_values = [
/*        'USD' => 'Доллар США',
        'EUR' => 'Евро',
        'RUB' => 'Рубль',
        'JPY' => 'Японская иена',
        'GBP' => 'Фунт стерлингов',
        'CHF' => 'Швейцарский франк',
        'CNY' => 'Юань'*/
        '$' => '$',
        '€' => '€',
        '₽' => '₽',
        '¥' => '¥',
        '£' => '£',
        '₣' => '₣',
        '元' => '元'
    ];

    public static function getValue($key)
    {
        if (!isset(static::$_values[$key])) {
            return $key;
        }
        return static::$_values[$key];
    }
}