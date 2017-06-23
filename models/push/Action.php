<?php
namespace app\models\push;

use app\components\Enum;

class Action extends Enum
{
    static $_values = [
        "NotifyAll",
        "NotifyToken",
        "NotifyReturn",
        "NotifyUpgrade",
    ];

    public static function NotifyAll()
    {
        return self::getKey("NotifyAll");
    }

    public static function NotifyToken()
    {
        return self::getKey("NotifyToken");
    }

    public static function NotifyReturn()
    {
        return self::getKey("NotifyReturn");
    }

    public static function NotifyUpgrade()
    {
        return self::getKey("NotifyUpgrade");
    }
}