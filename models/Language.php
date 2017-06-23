<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 11.05.15
 * Time: 3:02
 */

namespace app\models;


use app\components\Enum;

class Language extends Enum
{
    static $_values = [
        "ru" => "Russian",
        "en" => "English",
        "zh" => "Chinese",
        "es" => "Spanish",
        "ar" => "Arabic",
        "hi" => "Hindi",
        "bn" => "Bengali",
        "pt" => "Portuguese",
        "ja" => "Japanese",
        "de" => "German",
        "fr" => "French",
        "ko" => "Korean",
        "ta" => "Tamil",
        "it" => "Italian",
        "ur" => "Urdu",
        "tr" => "Turkish",
        "pl" => "Polish",
        "ms" => "Malay",
        "fa" => "Persian",
        "nl" => "Dutch",
    ];

    public static function getValue($key)
    {
        if (!isset(static::$_values[$key])) {
            return $key;
        }
        return static::$_values[$key];
    }
}