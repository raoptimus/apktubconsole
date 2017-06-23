<?php

namespace app\models;

use app\components\Enum;
use app\components\MyHtml as Html;

class Priority extends Enum
{
    static $_values = [
        'LOG_EMERG',
        'LOG_ALERT',
        'LOG_CRIT',
        'LOG_ERR',
        'LOG_WARNING',
        'LOG_NOTICE',
        'LOG_INFO',
        'LOG_DEBUG',
    ];

    public static function getIconValue($key) {
        switch(static::$_values[$key]) {
            case 'LOG_EMERG':
                return Html::glyphicon('fire',['data-level' => $key, 'style' => ['color'=>'#a94442', 'font-size' => '20px']]) . '&nbsp;' . static::$_values[$key] ;
            case 'LOG_ALERT':
                return Html::glyphicon('arrow-up',['data-level' => $key, 'style' => ['color'=>'#a94442', 'font-size' => '20px']]) . '&nbsp;' . static::$_values[$key] ;
            case 'LOG_CRIT':
                return Html::glyphicon('remove-circle',['data-level' => $key, 'style' => ['color'=>'#a94442', 'font-size' => '20px']]) . '&nbsp;' . static::$_values[$key] ;
            case 'LOG_ERR':
                return Html::glyphicon('remove',['data-level' => $key, 'style' => ['color'=>'#8a6d3b', 'font-size' => '20px']]) . '&nbsp;' . static::$_values[$key] ;
            case 'LOG_WARNING':
                return Html::glyphicon('warning-sign',['data-level' => $key, 'style' => ['color'=>'#8a6d3b', 'font-size' => '20px']]) . '&nbsp;' . static::$_values[$key] ;
            case 'LOG_NOTICE':
                return Html::glyphicon('question-sign',['data-level' => $key, 'style' => ['color'=>'#31708f', 'font-size' => '20px']]) . '&nbsp;' . static::$_values[$key] ;
            case 'LOG_INFO':
                return Html::glyphicon('info-sign',['data-level' => $key, 'style' => ['color'=>'#31708f', 'font-size' => '20px']]) . '&nbsp;' . static::$_values[$key] ;
            case 'LOG_DEBUG':
                return Html::glyphicon('dashboard',['data-level' => $key, 'style' => ['color'=>'#3c763d', 'font-size' => '20px']]) . '&nbsp;' . static::$_values[$key] ;
            default:
                return false;
        }
    }

    public static function getValue($key)
    {
        if (!isset(static::$_values[$key])) {
            return $key;
        }
        return static::$_values[$key];
    }
}