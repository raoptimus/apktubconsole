<?php
namespace app\models\push;

use app\components\Enum;

class Repeat extends Enum
{
    static $_values = [
        'Once',
        'Loop',
    ];
}